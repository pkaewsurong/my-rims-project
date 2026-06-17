<?php
// main/proposal_detail.php - รายละเอียดข้อเสนอโครงการ
$pageTitle = 'รายละเอียดข้อเสนอโครงการ';
$pageCss   = 'proposals';
$pageJs    = 'proposals';
require 'header.php';

$proposal_id = (int)($_GET['id'] ?? 0);
if (!$proposal_id) {
    echo '<div class="alert alert-danger">ไม่พบรหัสข้อเสนอโครงการ</div>';
    require 'footer.php';
    exit;
}

$user_id = authUser()['id'];
$isAdmin = hasRole('admin');

// Fetch proposal data
$stmt = $pdo->prepare('
    SELECT p.*, f.name as funding_source_name, u.name as leader_name, u.email as leader_email, pj.id as project_id
    FROM proposals p
    LEFT JOIN funding_sources f ON p.funding_source_id = f.id
    LEFT JOIN users u ON p.user_id = u.id
    LEFT JOIN projects pj ON p.id = pj.proposal_id
    WHERE p.id = ?
');
$stmt->execute([$proposal_id]);
$proposal = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$proposal) {
    echo '<div class="alert alert-danger">ไม่พบข้อมูลข้อเสนอโครงการ</div>';
    require 'footer.php';
    exit;
}

// Access check
if (!$isAdmin && $proposal['user_id'] != $user_id) {
    echo '<div class="alert alert-danger">คุณไม่มีสิทธิ์เข้าถึงข้อเสนอโครงการนี้</div>';
    require 'footer.php';
    exit;
}

// Fetch team members
$teamStmt = $pdo->prepare('SELECT * FROM proposal_teams WHERE proposal_id = ?');
$teamStmt->execute([$proposal_id]);
$teams = $teamStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch reviews
$reviewStmt = $pdo->prepare('
    SELECT pr.*, u.name as reviewer_name 
    FROM project_reviews pr 
    LEFT JOIN users u ON pr.reviewer_id = u.id 
    WHERE pr.proposal_id = ?
');
$reviewStmt->execute([$proposal_id]);
$reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);

$statusClass = match(strtolower($proposal['status'] ?? '')) {
    'draft' => 'bg-secondary text-white',
    'submitted' => 'bg-info text-dark',
    'under_review' => 'bg-warning text-dark',
    'needs_revision' => 'bg-danger text-white',
    'approved' => 'bg-success text-white',
    'rejected' => 'bg-danger text-white',
    default => 'bg-dark text-white'
};
?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-4">
    <div>
        <h1 class="page-title"><i class="ri-file-list-3-line me-2"></i>ข้อเสนอโครงการ</h1>
        <nav aria-label="breadcrumb" class="mt-1">
            <ol class="breadcrumb mb-0" style="font-size:13px;">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">หน้าหลัก</a></li>
                <li class="breadcrumb-item"><a href="projects.php" class="text-decoration-none">โครงการ & ข้อเสนอ</a></li>
                <li class="breadcrumb-item active">รายละเอียดข้อเสนอ</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <?php if (in_array($proposal['status'], ['submitted', 'under_review', 'needs_revision']) && $isAdmin): ?>
        <button onclick="reviewProposal(<?php echo $proposal['id']; ?>)" class="btn btn-dark fw-bold">
            <i class="ri-check-double-line me-1"></i> ประเมินข้อเสนอโครงการ
        </button>
        <?php endif; ?>
        <?php if (in_array($proposal['status'], ['draft', 'needs_revision']) && $proposal['user_id'] == $user_id): ?>
        <a href="proposal_form.php?id=<?php echo $proposal['id']; ?>" class="btn btn-primary fw-bold">
            <i class="ri-edit-2-line me-1"></i> แก้ไขข้อเสนอโครงการ
        </a>
        <?php endif; ?>
        <a href="projects.php" class="btn btn-outline-secondary">
            <i class="ri-arrow-left-line me-1"></i> ย้อนกลับ
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Main content column -->
    <div class="col-lg-8">
        <!-- Title & abstract -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">
                <span class="badge <?php echo $statusClass; ?> mb-3 px-2 py-1 fs-6">
                    <?php echo match(strtolower($proposal['status'] ?? '')) {
                        'draft' => 'แบบร่าง',
                        'submitted' => 'รอพิจารณา',
                        'under_review' => 'อยู่ระหว่างการประเมิน',
                        'needs_revision' => 'ให้กลับไปแก้ไข',
                        'approved' => 'อนุมัติแล้ว',
                        'rejected' => 'ปฏิเสธ',
                        default => $proposal['status']
                    }; ?>
                </span>

                <?php if ($proposal['status'] === 'needs_revision' && !empty($proposal['revision_comment'])): ?>
                <div class="alert alert-danger border border-danger d-flex align-items-start gap-2 mb-4 p-3 shadow-sm">
                    <i class="ri-error-warning-line text-danger fs-4 leading-none mt-0.5"></i>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">สิ่งที่ควรปรับปรุงแก้ไขตามมติกรรมการ:</h6>
                        <p class="text-secondary mb-0 small whitespace-pre-wrap"><?php echo htmlspecialchars($proposal['revision_comment']); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <h4 class="fw-bold text-dark mb-2"><?php echo htmlspecialchars($proposal['title']); ?></h4>
                <p class="text-muted mb-4"><em><?php echo htmlspecialchars($proposal['title_en'] ?? ''); ?></em></p>
                
                <hr>

                <h5 class="fw-bold text-dark mt-4 mb-2"><i class="ri-article-line me-1 text-muted"></i> บทคัดย่อ (Abstract)</h5>
                <p class="text-secondary whitespace-pre-wrap leading-relaxed" style="text-align: justify;">
                    <?php echo htmlspecialchars($proposal['abstract'] ?? 'ไม่มีบทคัดย่อ'); ?>
                </p>

                <h5 class="fw-bold text-dark mt-4 mb-2"><i class="ri-focus-3-line me-1 text-muted"></i> ความเชื่อมโยงเชิงยุทธศาสตร์</h5>
                <p class="text-secondary whitespace-pre-wrap"><?php echo htmlspecialchars($proposal['strategic_link'] ?? 'ไม่ได้ระบุ'); ?></p>

                <h5 class="fw-bold text-dark mt-4 mb-2"><i class="ri-line-chart-line me-1 text-muted"></i> ตัวชี้วัดผลกระทบ</h5>
                <p class="text-secondary"><?php echo htmlspecialchars($proposal['impact_indicator'] ?? 'ไม่ได้ระบุ'); ?></p>
            </div>
        </div>

        <!-- Team members -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">
                <h5 class="fw-bold text-dark mb-3"><i class="ri-team-line me-1 text-muted"></i> ทีมงานวิจัย</h5>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ชื่อ-นามสกุล</th>
                                <th>บทบาทหน้าที่</th>
                                <th class="text-center" style="width: 150px;">สัดส่วนภาระงาน</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold"><?php echo htmlspecialchars($proposal['leader_name']); ?></td>
                                <td><span class="badge bg-dark">หัวหน้าโครงการ (PI)</span></td>
                                <td class="text-center fw-bold text-dark"><?php echo $proposal['pi_proportion'] ?? 100; ?>%</td>
                            </tr>
                            <?php foreach ($teams as $t): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($t['name']); ?></td>
                                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($t['role']); ?></span></td>
                                    <td class="text-center fw-bold"><?php echo $t['proportion']; ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Milestones list -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">
                <h5 class="fw-bold text-dark mb-3"><i class="ri-flag-line me-1 text-muted"></i> แผนดำเนินงานและเป้าหมายย่อย (Milestones)</h5>
                
                <?php 
                $milestones_array = [];
                if (!empty($proposal['milestones'])) {
                    $milestones_array = json_decode($proposal['milestones'], true);
                }
                ?>
                
                <?php if (empty($milestones_array)): ?>
                    <p class="text-muted italic small py-2 mb-0">ไม่มีข้อมูลแผนการดำเนินงาน</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($milestones_array as $index => $ms): ?>
                            <div class="list-group-item p-3">
                                <h6 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($ms['name'] ?? ''); ?></h6>
                                <p class="text-muted small mb-0"><?php echo htmlspecialchars($ms['description'] ?? ''); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Reviews section -->
        <?php if (!empty($reviews)): ?>
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">
                <h5 class="fw-bold text-dark mb-3"><i class="ri-check-double-line me-1 text-muted"></i> ผลการประเมินจากกรรมการ</h5>
                
                <?php foreach ($reviews as $rev): ?>
                    <div class="bg-light p-3 rounded mb-3 border border-slate-100">
                        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                            <span class="fw-bold text-dark">โดยกรรมการ: <?php echo htmlspecialchars($rev['reviewer_name']); ?></span>
                            <span class="badge bg-dark fs-6">คะแนนรวม: <?php echo $rev['total_score']; ?> / 100</span>
                        </div>
                        <div class="row g-2 mb-3 mt-1 small">
                            <div class="col-md-3"><strong>แนวคิด/ความสำคัญ:</strong> <?php echo $rev['score_concept']; ?>/25</div>
                            <div class="col-md-3"><strong>ความพร้อมทีมวิจัย:</strong> <?php echo $rev['score_team']; ?>/25</div>
                            <div class="col-md-3"><strong>ความสอดคล้องยุทธศาสตร์:</strong> <?php echo $rev['score_alignment']; ?>/25</div>
                            <div class="col-md-3"><strong>ผลกระทบโครงการ:</strong> <?php echo $rev['score_impact']; ?>/25</div>
                        </div>
                        <?php if ($rev['comments_strengths']): ?>
                            <div class="mb-2">
                                <strong class="small text-muted d-block">จุดเด่นของโครงการ:</strong>
                                <span class="small text-dark"><?php echo htmlspecialchars($rev['comments_strengths']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($rev['comments_suggestions']): ?>
                            <div>
                                <strong class="small text-muted d-block">ข้อเสนอแนะเพิ่มเติม:</strong>
                                <span class="small text-dark"><?php echo htmlspecialchars($rev['comments_suggestions']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Side details column -->
    <div class="col-lg-4">
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">
                <h5 class="fw-bold text-dark mb-4">ข้อมูลการส่งทุน</h5>

                <div class="mb-3">
                    <label class="text-muted small d-block">งบประมาณรวม</label>
                    <span class="fw-bold text-success fs-5"><?php echo number_format($proposal['budget_total'] ?? 0, 2); ?> ฿</span>
                </div>

                <div class="mb-3">
                    <label class="text-muted small d-block">แหล่งทุนวิจัย</label>
                    <span class="fw-bold text-dark"><?php echo htmlspecialchars($proposal['funding_source_name'] ?? 'ไม่ระบุ'); ?></span>
                </div>

                <div class="mb-3">
                    <label class="text-muted small d-block">วันที่ยื่นเรื่อง</label>
                    <span class="fw-bold text-dark"><?php echo $proposal['created_at'] ? date('d M Y', strtotime($proposal['created_at'])) : '-'; ?></span>
                </div>

                <div class="mb-3">
                    <label class="text-muted small d-block">รหัสโครงการหลังอนุมัติ</label>
                    <?php if ($proposal['project_id']): ?>
                    <a href="project_detail.php?id=<?php echo $proposal['project_id']; ?>" class="badge bg-success text-decoration-none">
                        <i class="ri-folder-open-line me-1"></i> ดูโครงการวิจัย
                    </a>
                    <?php else: ?>
                    <span class="text-muted small italic">ยังไม่เป็นโครงการ</span>
                    <?php endif; ?>
                </div>

                <hr>

                <h5 class="fw-bold text-dark my-3">ไฟล์เอกสารแนบ</h5>
                
                <div class="d-grid gap-2">
                    <?php if ($proposal['file_proposal']): ?>
                    <a href="../public/uploads/proposals/<?php echo htmlspecialchars($proposal['file_proposal']); ?>" target="_blank" class="btn btn-sm btn-outline-dark text-start">
                        <i class="ri-file-pdf-line me-1 text-danger"></i> เอกสารข้อเสนอวิจัย
                    </a>
                    <?php else: ?>
                    <button class="btn btn-sm btn-outline-secondary text-start" disabled>
                        <i class="ri-file-line me-1"></i> ไม่มีเอกสารข้อเสนอ
                    </button>
                    <?php endif; ?>

                    <?php if ($proposal['file_budget']): ?>
                    <a href="../public/uploads/proposals/<?php echo htmlspecialchars($proposal['file_budget']); ?>" target="_blank" class="btn btn-sm btn-outline-dark text-start">
                        <i class="ri-file-excel-line me-1 text-success"></i> เอกสารงบประมาณ
                    </a>
                    <?php endif; ?>

                    <?php if ($proposal['file_cv']): ?>
                    <a href="../public/uploads/proposals/<?php echo htmlspecialchars($proposal['file_cv']); ?>" target="_blank" class="btn btn-sm btn-outline-dark text-start">
                        <i class="ri-user-shared-line me-1 text-primary"></i> ประวัติหัวหน้าโครงการ (CV)
                    </a>
                    <?php endif; ?>

                    <?php if ($proposal['file_ethics']): ?>
                    <a href="../public/uploads/proposals/<?php echo htmlspecialchars($proposal['file_ethics']); ?>" target="_blank" class="btn btn-sm btn-outline-dark text-start">
                        <i class="ri-shield-check-line me-1 text-warning"></i> เอกสารรับรองจริยธรรม
                    </a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- AJAX Modal สำหรับประเมินข้อเสนอ -->
<div class="modal fade" id="mainModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" id="showModal"></div>
    </div>
</div>

<?php require 'footer.php'; ?>
