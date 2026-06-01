<?php
// src/controllers/ProposalController.php

function proposalIndexAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }
    
    // Fetch proposals for user or admin
    $user_id = authUser()['id'];
    if (hasRole('admin')) {
        $stmt = $pdo->prepare('
            SELECT p.*, f.name as funding_source_name, u.name as pi_name, pj.closure_requested, pj.id as project_id
            FROM proposals p
            LEFT JOIN funding_sources f ON p.funding_source_id = f.id
            LEFT JOIN users u ON p.user_id = u.id
            LEFT JOIN projects pj ON p.id = pj.proposal_id
            WHERE p.status != "closed" AND p.status != "draft" AND (pj.status IS NULL OR pj.status != "closed")
            ORDER BY p.created_at DESC
        ');
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare('
            SELECT p.*, f.name as funding_source_name, u.name as pi_name, pj.closure_requested, pj.id as project_id
            FROM proposals p
            LEFT JOIN funding_sources f ON p.funding_source_id = f.id
            LEFT JOIN users u ON p.user_id = u.id
            LEFT JOIN projects pj ON p.id = pj.proposal_id
            WHERE p.user_id = ? AND p.status != "closed" AND (pj.status IS NULL OR pj.status != "closed")
            ORDER BY p.created_at DESC
        ');
        $stmt->execute([$user_id]);
    }
    $proposals = $stmt->fetchAll();

    require __DIR__ . '/../../views/projects/proposals.php';
}

function proposalCreateAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }

    $stmt = $pdo->query('SELECT * FROM funding_sources');
    $funding_sources = $stmt->fetchAll();

    $proposal = null; // New proposal mode
    $teams = [];

    require __DIR__ . '/../../views/projects/create-proposal.php';
}

function proposalEditAction($pdo, $id) {
    if (!isLoggedIn()) {
        redirect('/login');
    }

    $user_id = authUser()['id'];

    // Load proposal
    if (hasRole('admin')) {
        $stmt = $pdo->prepare('
            SELECT p.*, u.name as researcher_name 
            FROM proposals p 
            LEFT JOIN users u ON p.user_id = u.id 
            WHERE p.id = ?
        ');
        $stmt->execute([$id]);
    } else {
        $stmt = $pdo->prepare('SELECT *, NULL as researcher_name FROM proposals WHERE id = ? AND user_id = ? AND status = "draft"');
        $stmt->execute([$id, $user_id]);
    }
    $proposal = $stmt->fetch();

    if (!$proposal) {
        die("ไม่พบข้อเสนอโครงการฉบับร่าง หรือไม่มีสิทธิ์แก้ไข");
    }

    // Load teams
    $teamStmt = $pdo->prepare('SELECT * FROM proposal_teams WHERE proposal_id = ?');
    $teamStmt->execute([$id]);
    $teams = $teamStmt->fetchAll(PDO::FETCH_ASSOC);

    // Load funding sources for dropdown
    $stmt = $pdo->query('SELECT * FROM funding_sources');
    $funding_sources = $stmt->fetchAll();

    require __DIR__ . '/../../views/projects/create-proposal.php';
}

function proposalUpdateAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = authUser()['id'];
        $proposal_id = $_POST['proposal_id'] ?? null;

        if (!$proposal_id) {
            die("Proposal ID is required");
        }

        // Verify ownership and draft status
        $checkStmt = $pdo->prepare('SELECT id FROM proposals WHERE id = ? AND user_id = ? AND status = "draft"');
        $checkStmt->execute([$proposal_id, $user_id]);
        if (!$checkStmt->fetch()) {
            die("ไม่พบข้อเสนอโครงการฉบับร่าง หรือไม่มีสิทธิ์แก้ไข");
        }

        // Collect form data (same as store)
        $title = $_POST['title'] ?? '';
        $title_en = $_POST['title_en'] ?? '';
        $research_type = $_POST['research_type'] ?? '';
        $keywords = $_POST['keywords'] ?? '';
        $abstract = $_POST['abstract'] ?? '';

        $team_proportions = $_POST['team_proportion'] ?? [];
        $total_coi_prop = 0;
        foreach ($team_proportions as $prop) {
            $total_coi_prop += (int)$prop;
        }
        $pi_proportion = max(0, 100 - $total_coi_prop);

        $budget_total = !empty($_POST['budget_total']) ? (float)str_replace(',', '', $_POST['budget_total']) : 0;
        $funding_source_id = !empty($_POST['funding_source_id']) ? $_POST['funding_source_id'] : null;
        $budget_details = $_POST['budget_details'] ?? '';
        $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
        $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;

        $milestone_names = $_POST['milestone_name'] ?? [];
        $milestone_descs = $_POST['milestone_description'] ?? [];
        $milestones_array = [];
        for ($i = 0; $i < count($milestone_names); $i++) {
            if (!empty(trim($milestone_names[$i]))) {
                $milestones_array[] = [
                    'name' => trim($milestone_names[$i]),
                    'description' => trim($milestone_descs[$i] ?? '')
                ];
            }
        }
        $milestones = !empty($milestones_array) ? json_encode($milestones_array, JSON_UNESCAPED_UNICODE) : '';

        $strategic_link = $_POST['strategic_link'] ?? '';
        $impact_indicator = $_POST['impact_indicator'] ?? '';
        $expected_outputs = $_POST['expected_outputs'] ?? [];
        $expected_outputs_json = is_array($expected_outputs) ? json_encode($expected_outputs, JSON_UNESCAPED_UNICODE) : '[]';

        $status = $_POST['status'] ?? 'draft';
        if (!in_array($status, ['draft', 'submitted'])) {
            $status = 'draft';
        }

        if ($status === 'submitted' && empty($title)) {
            die("Title is required");
        }
        if (empty($title)) {
            $title = '(ร่าง) ยังไม่มีชื่อ';
        }

        // Handle File Uploads (keep old files if no new upload)
        $upload_dir = __DIR__ . '/../../public/uploads/proposals/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_fields = ['file_proposal', 'file_budget', 'file_cv', 'file_ethics'];
        $file_updates = [];
        $file_values = [];

        foreach ($file_fields as $field) {
            if (!empty($_FILES[$field]['name'])) {
                $filename = time() . '_' . substr($field, 5) . '_' . basename($_FILES[$field]['name']);
                move_uploaded_file($_FILES[$field]['tmp_name'], $upload_dir . $filename);
                $file_updates[] = "$field = ?";
                $file_values[] = $filename;
            }
        }

        $fileSql = !empty($file_updates) ? ', ' . implode(', ', $file_updates) : '';

        // Update proposal
        $stmt = $pdo->prepare("
            UPDATE proposals SET
                title = ?, title_en = ?, research_type = ?, keywords = ?, abstract = ?, pi_proportion = ?,
                budget_total = ?, funding_source_id = ?, budget_details = ?, start_date = ?, end_date = ?,
                milestones = ?, strategic_link = ?, impact_indicator = ?, expected_outputs = ?,
                status = ?, updated_at = NOW()
                $fileSql
            WHERE id = ? AND user_id = ?
        ");

        $params = [
            $title, $title_en, $research_type, $keywords, $abstract, $pi_proportion,
            $budget_total, $funding_source_id, $budget_details, $start_date, $end_date,
            $milestones, $strategic_link, $impact_indicator, $expected_outputs_json,
            $status
        ];
        $params = array_merge($params, $file_values);
        $params[] = $proposal_id;
        $params[] = $user_id;

        $stmt->execute($params);

        // Update team members: delete old, insert new
        $pdo->prepare('DELETE FROM proposal_teams WHERE proposal_id = ?')->execute([$proposal_id]);

        if (isset($_POST['team_name']) && is_array($_POST['team_name'])) {
            $team_names = $_POST['team_name'];
            $team_roles = $_POST['team_role'] ?? [];
            $team_proportions = $_POST['team_proportion'] ?? [];

            $teamStmt = $pdo->prepare('INSERT INTO proposal_teams (proposal_id, name, role, proportion, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())');

            for ($i = 0; $i < count($team_names); $i++) {
                $name = $team_names[$i];
                if (!empty(trim($name))) {
                    $role = $team_roles[$i] ?? 'Co-Investigator';
                    $proportion = !empty($team_proportions[$i]) ? (int)$team_proportions[$i] : 0;
                    $teamStmt->execute([$proposal_id, trim($name), $role, $proportion]);
                }
            }
        }

        $redirectTarget = ($status === 'draft') ? '/projects' : '/proposals';
        redirect($redirectTarget);
    }
}

function proposalStoreAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = authUser()['id'];

        // STEP 1 Data
        $title = $_POST['title'] ?? '';
        $title_en = $_POST['title_en'] ?? '';
        $research_type = $_POST['research_type'] ?? '';
        $keywords = $_POST['keywords'] ?? '';
        $abstract = $_POST['abstract'] ?? '';
        
        // Calculate PI Proportion server-side for safety
        $team_proportions = $_POST['team_proportion'] ?? [];
        $total_coi_prop = 0;
        foreach ($team_proportions as $prop) {
            $total_coi_prop += (int)$prop;
        }
        $pi_proportion = 100 - $total_coi_prop;
        
        if ($pi_proportion < 0) {
            die("สัดส่วนการทำงานรวมเกิน 100% กรุณาปรับลดสัดส่วนของผู้ร่วมวิจัย");
        }

        // STEP 2 Data
        $budget_total = !empty($_POST['budget_total']) ? (float)$_POST['budget_total'] : 0;
        $funding_source_id = !empty($_POST['funding_source_id']) ? $_POST['funding_source_id'] : null;
        $budget_details = $_POST['budget_details'] ?? '';
        $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
        $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
        $milestone_names = $_POST['milestone_name'] ?? [];
        $milestone_descs = $_POST['milestone_description'] ?? [];
        $milestones_array = [];
        for ($i = 0; $i < count($milestone_names); $i++) {
            if (!empty(trim($milestone_names[$i]))) {
                $milestones_array[] = [
                    'name' => trim($milestone_names[$i]),
                    'description' => trim($milestone_descs[$i] ?? '')
                ];
            }
        }
        $milestones = !empty($milestones_array) ? json_encode($milestones_array, JSON_UNESCAPED_UNICODE) : '';

        // STEP 3 Data
        $strategic_link = $_POST['strategic_link'] ?? '';
        $impact_indicator = $_POST['impact_indicator'] ?? '';
        $expected_outputs = $_POST['expected_outputs'] ?? [];
        $expected_outputs_json = is_array($expected_outputs) ? json_encode($expected_outputs, JSON_UNESCAPED_UNICODE) : '[]';
        // Determine status from form
        $status = $_POST['status'] ?? 'draft';
        if (!in_array($status, ['draft', 'submitted'])) {
            $status = 'draft';
        }

        if ($status === 'submitted' && empty($title)) {
            die("Title is required");
        }
        // For draft, allow empty title with a placeholder
        if (empty($title)) {
            $title = '(ร่าง) ยังไม่มีชื่อ';
        }

        // Handle File Uploads
        $upload_dir = __DIR__ . '/../../public/uploads/proposals/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_proposal = null;
        $file_budget = null;
        $file_cv = null;

        if (!empty($_FILES['file_proposal']['name'])) {
            $file_proposal = time() . '_prop_' . basename($_FILES['file_proposal']['name']);
            move_uploaded_file($_FILES['file_proposal']['tmp_name'], $upload_dir . $file_proposal);
        }
        if (!empty($_FILES['file_budget']['name'])) {
            $file_budget = time() . '_budg_' . basename($_FILES['file_budget']['name']);
            move_uploaded_file($_FILES['file_budget']['tmp_name'], $upload_dir . $file_budget);
        }
        if (!empty($_FILES['file_cv']['name'])) {
            $file_cv = time() . '_cv_' . basename($_FILES['file_cv']['name']);
            move_uploaded_file($_FILES['file_cv']['tmp_name'], $upload_dir . $file_cv);
        }
        $file_ethics = null;
        if (!empty($_FILES['file_ethics']['name'])) {
            $file_ethics = time() . '_ethics_' . basename($_FILES['file_ethics']['name']);
            move_uploaded_file($_FILES['file_ethics']['tmp_name'], $upload_dir . $file_ethics);
        }

        $stmt = $pdo->prepare('
            INSERT INTO proposals (
                title, title_en, research_type, keywords, abstract, pi_proportion, 
                budget_total, funding_source_id, budget_details, start_date, end_date, 
                milestones, strategic_link, impact_indicator, expected_outputs, 
                file_proposal, file_budget, file_cv, file_ethics,
                user_id, status, created_at, updated_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?, 
                ?, ?, ?, ?, ?, 
                ?, ?, ?, ?, 
                ?, ?, ?, ?,
                ?, ?, NOW(), NOW()
            )
        ');
        
        $stmt->execute([
            $title, $title_en, $research_type, $keywords, $abstract, $pi_proportion,
            $budget_total, $funding_source_id, $budget_details, $start_date, $end_date,
            $milestones, $strategic_link, $impact_indicator, $expected_outputs_json, 
            $file_proposal, $file_budget, $file_cv, $file_ethics,
            $user_id, $status
        ]);
        
        $proposal_id = $pdo->lastInsertId();

        // Handle dynamic researchers
        if (isset($_POST['team_name']) && is_array($_POST['team_name'])) {
            $team_names = $_POST['team_name'];
            $team_roles = $_POST['team_role'] ?? [];
            $team_proportions = $_POST['team_proportion'] ?? [];
            
            $teamStmt = $pdo->prepare('INSERT INTO proposal_teams (proposal_id, name, role, proportion, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())');
            
            for ($i = 0; $i < count($team_names); $i++) {
                $name = $team_names[$i];
                if (!empty(trim($name))) {
                    $role = $team_roles[$i] ?? 'Co-Investigator';
                    $proportion = !empty($team_proportions[$i]) ? (int)$team_proportions[$i] : 0;
                    $teamStmt->execute([$proposal_id, trim($name), $role, $proportion]);
                }
            }
        }
        
        $redirectTarget = ($status === 'draft') ? '/projects' : '/proposals';
        redirect($redirectTarget);
    }
}

function proposalReviewAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }
    requireRole('admin');
    
    $proposal_id = $_GET['id'] ?? null;
    if (!$proposal_id) {
        die("Proposal ID is required");
    }

    $stmt = $pdo->prepare('
        SELECT p.*, f.name as funding_source_name, u.name as user_name
        FROM proposals p
        LEFT JOIN funding_sources f ON p.funding_source_id = f.id
        LEFT JOIN users u ON p.user_id = u.id
        WHERE p.id = ?
    ');
    $stmt->execute([$proposal_id]);
    $proposal = $stmt->fetch();

    if (!$proposal) {
        die("Proposal not found");
    }

    // Fetch team
    $teamStmt = $pdo->prepare('SELECT * FROM proposal_teams WHERE proposal_id = ?');
    $teamStmt->execute([$proposal_id]);
    $teams = $teamStmt->fetchAll();

    // Fetch existing review if any
    $reviewer_id = authUser()['id'];
    $reviewStmt = $pdo->prepare('SELECT * FROM project_reviews WHERE proposal_id = ? AND reviewer_id = ?');
    $reviewStmt->execute([$proposal_id, $reviewer_id]);
    $review = $reviewStmt->fetch();

    require __DIR__ . '/../../views/projects/review.php';
}

function proposalShowAction($pdo, $id) {
    if (!isLoggedIn()) {
        redirect('/login');
    }
    
    $user_id = authUser()['id'];
    $isAdmin = hasRole('admin');

    $stmt = $pdo->prepare('
        SELECT p.*, f.name as funding_source_name, u.name as user_name, pj.id as project_id, pj.closure_requested
        FROM proposals p
        LEFT JOIN funding_sources f ON p.funding_source_id = f.id
        LEFT JOIN users u ON p.user_id = u.id
        LEFT JOIN projects pj ON p.id = pj.proposal_id
        WHERE p.id = ?
    ');
    $stmt->execute([$id]);
    $proposal = $stmt->fetch();

    if (!$proposal) {
        die("Proposal not found");
    }

    if (!$isAdmin && $proposal['user_id'] != $user_id) {
        die("Access denied: You do not have permission to view this proposal.");
    }

    // Fetch team
    $teamStmt = $pdo->prepare('SELECT * FROM proposal_teams WHERE proposal_id = ?');
    $teamStmt->execute([$id]);
    $teams = $teamStmt->fetchAll();

    require __DIR__ . '/../../views/projects/show-proposal.php';
}

function proposalStoreReviewAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }
    requireRole('admin');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $proposal_id = $_POST['proposal_id'] ?? null;
        $reviewer_id = authUser()['id'];
        
        $score_concept = (int)($_POST['score_concept'] ?? 0);
        $score_team = (int)($_POST['score_team'] ?? 0);
        $score_alignment = (int)($_POST['score_alignment'] ?? 0);
        $score_impact = (int)($_POST['score_impact'] ?? 0);
        
        $total_score = $score_concept + $score_team + $score_alignment + $score_impact;
        
        $comments_strengths = $_POST['comments_strengths'] ?? '';
        $comments_suggestions = $_POST['comments_suggestions'] ?? '';
        
        $status = $_POST['status'] ?? 'under_review'; // 'approved', 'rejected', 'under_review', 'draft'
        
        $is_draft = isset($_POST['is_draft']) && $_POST['is_draft'] === '1' ? 1 : 0;
        
        if ($is_draft === 1) {
            $status = 'under_review'; // Override status for draft
        }

        if (!$proposal_id) {
            die("Proposal ID is required");
        }

        // Prevent modification if already approved
        $propCheck = $pdo->prepare('SELECT status FROM proposals WHERE id = ?');
        $propCheck->execute([$proposal_id]);
        $propData = $propCheck->fetch();
        if ($propData && $propData['status'] === 'approved') {
            die("ข้อเสนอโครงการนี้ได้รับการอนุมัติไปแล้ว ไม่สามารถแก้ไขผลการประเมินได้อีก");
        }

        // Check if review exists
        $checkStmt = $pdo->prepare('SELECT id FROM project_reviews WHERE proposal_id = ? AND reviewer_id = ?');
        $checkStmt->execute([$proposal_id, $reviewer_id]);
        $existingReview = $checkStmt->fetch();

        if ($existingReview) {
            // Update Review
            $stmt = $pdo->prepare('
                UPDATE project_reviews 
                SET score_concept = ?, score_team = ?, score_alignment = ?, score_impact = ?, 
                    total_score = ?, comments_strengths = ?, comments_suggestions = ?, 
                    status = ?, updated_at = NOW()
                WHERE id = ?
            ');
            $stmt->execute([
                $score_concept, $score_team, $score_alignment, $score_impact,
                $total_score, $comments_strengths, $comments_suggestions, 
                $status, $existingReview['id']
            ]);
        } else {
            // Insert Review
            $stmt = $pdo->prepare('
                INSERT INTO project_reviews (
                    proposal_id, reviewer_id, score_concept, score_team, 
                    score_alignment, score_impact, total_score, 
                    comments_strengths, comments_suggestions, status, 
                    created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ');
            $stmt->execute([
                $proposal_id, $reviewer_id, $score_concept, $score_team,
                $score_alignment, $score_impact, $total_score,
                $comments_strengths, $comments_suggestions, $status
            ]);
        }

        // Update Proposal Status
        if ($is_draft === 1) {
            $updateStmt = $pdo->prepare('UPDATE proposals SET status = ?, updated_at = NOW() WHERE id = ? AND status != "approved" AND status != "rejected"');
            $updateStmt->execute(['under_review', $proposal_id]);
        } else {
            $updateStmt = $pdo->prepare('UPDATE proposals SET status = ?, updated_at = NOW() WHERE id = ?');
            $updateStmt->execute([$status, $proposal_id]);

            // If status is approved, and it doesn't have a project yet, create one
            if ($status === 'approved') {
                $checkProj = $pdo->prepare('SELECT id FROM projects WHERE proposal_id = ?');
                $checkProj->execute([$proposal_id]);
                if (!$checkProj->fetch()) {
                    // Generate PROJ-XXXX code
                    $maxCode = $pdo->query("SELECT MAX(CAST(SUBSTRING(code, 6) AS UNSIGNED)) FROM projects")->fetchColumn() ?: 0;
                    $newCode = 'PROJ-' . str_pad($maxCode + 1, 4, '0', STR_PAD_LEFT);
                    
                    // Get proposal data for start/end date and milestones
                    $propStmt = $pdo->prepare('SELECT start_date, end_date, milestones FROM proposals WHERE id = ?');
                    $propStmt->execute([$proposal_id]);
                    $propData = $propStmt->fetch();

                    $createProj = $pdo->prepare('
                        INSERT INTO projects (proposal_id, code, start_date, end_date, status, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, "ongoing", NOW(), NOW())
                    ');
                    $createProj->execute([
                        $proposal_id, 
                        $newCode, 
                        $propData['start_date'] ?? date('Y-m-d'), 
                        $propData['end_date'] ?? date('Y-m-d', strtotime('+1 year'))
                    ]);
                    
                    $new_project_id = $pdo->lastInsertId();
                    
                    // Insert milestones
                    if (!empty($propData['milestones'])) {
                        $milestones_array = json_decode($propData['milestones'], true);
                        if (is_array($milestones_array)) {
                            $msStmt = $pdo->prepare('INSERT INTO project_milestones (project_id, milestone_name, description, status, created_at, updated_at) VALUES (?, ?, ?, "pending", NOW(), NOW())');
                            foreach ($milestones_array as $ms) {
                                $msStmt->execute([
                                    $new_project_id,
                                    $ms['name'] ?? '',
                                    $ms['description'] ?? ''
                                ]);
                            }
                        }
                    }
                }
            }
        }

        redirect('/proposals');
    }
}

function proposalDeleteAction($pdo, $id) {
    if (!isLoggedIn()) {
        redirect('/login');
    }
    
    $user_id = authUser()['id'];
    
    // Verify ownership and draft status
    $stmt = $pdo->prepare('SELECT * FROM proposals WHERE id = ? AND user_id = ? AND status = "draft"');
    $stmt->execute([$id, $user_id]);
    $proposal = $stmt->fetch();
    
    if (!$proposal) {
        die("ไม่พบข้อเสนอโครงการฉบับร่าง หรือไม่มีสิทธิ์ลบ");
    }
    
    // Delete files if they exist
    $upload_dir = __DIR__ . '/../../public/uploads/proposals/';
    $file_fields = ['file_proposal', 'file_budget', 'file_cv', 'file_ethics'];
    foreach ($file_fields as $field) {
        if (!empty($proposal[$field])) {
            $filepath = $upload_dir . $proposal[$field];
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }
    }
    
    // Delete related team members
    $pdo->prepare('DELETE FROM proposal_teams WHERE proposal_id = ?')->execute([$id]);
    
    // Delete the proposal
    $pdo->prepare('DELETE FROM proposals WHERE id = ?')->execute([$id]);
    
    redirect('/projects');
}
