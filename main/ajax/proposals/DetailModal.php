<?php
// main/ajax/proposals/DetailModal.php - รายละเอียดข้อเสนอโครงการ (Modal)
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');

if (!isLoggedIn()) { http_response_code(401); exit; }

$proposal_id = (int)($_GET['id'] ?? 0);
if (!$proposal_id) { echo '<div class="alert alert-danger m-3">ไม่พบรหัสข้อเสนอโครงการ</div>'; exit; }

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

if (!$proposal) { echo '<div class="alert alert-danger m-3">ไม่พบข้อมูลข้อเสนอโครงการ</div>'; exit; }

// Access check
if (!$isAdmin && $proposal['user_id'] != $user_id) {
    echo '<div class="alert alert-danger m-3">คุณไม่มีสิทธิ์เข้าถึงข้อเสนอโครงการนี้</div>';
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
    'draft' => 'bg-secondary',
    'submitted' => 'bg-info text-dark',
    'under_review' => 'bg-warning text-dark',
    'needs_revision' => 'bg-danger text-white',
    'approved' => 'bg-success',
    'rejected' => 'bg-danger',
    default => 'bg-dark'
};
?>
<div class="modal-header">
    <h5 class="modal-title fw-bold">
        <i class="ri-file-list-3-line me-2"></i> รายละเอียดข้อเสนอโครงการ
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body p-0">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs nav-fill border-bottom" id="modalProposalTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active py-2.5 fw-bold text-dark border-0 rounded-0" id="mp-info-tab" data-bs-toggle="tab" data-bs-target="#mp-info-pane" type="button" role="tab">
                ข้อมูลข้อเสนอ
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link py-2.5 fw-bold text-dark border-0 rounded-0" id="mp-team-tab" data-bs-toggle="tab" data-bs-target="#mp-team-pane" type="button" role="tab">
                ทีมวิจัย (<?php echo count($teams) + 1; ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link py-2.5 fw-bold text-dark border-0 rounded-0" id="mp-milestones-tab" data-bs-toggle="tab" data-bs-target="#mp-milestones-pane" type="button" role="tab">
                แผนงาน
            </button>
        </li>
        <?php if (!empty($reviews)): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link py-2.5 fw-bold text-dark border-0 rounded-0" id="mp-reviews-tab" data-bs-toggle="tab" data-bs-target="#mp-reviews-pane" type="button" role="tab">
                ผลประเมิน (<?php echo count($reviews); ?>)
            </button>
        </li>
        <?php endif; ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link py-2.5 fw-bold text-dark border-0 rounded-0" id="mp-files-tab" data-bs-toggle="tab" data-bs-target="#mp-files-pane" type="button" role="tab">
                เอกสารแนบ
            </button>
        </li>
    </ul>

    <div class="tab-content p-4" id="modalProposalTabContent" style="max-height: 60vh; overflow-y: auto;">
        <!-- TAB 1: ข้อมูลทั่วไป -->
        <div class="tab-pane fade show active" id="mp-info-pane" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge <?php echo $statusClass; ?> px-2.5 py-1.5 fs-6 rounded-pill">
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
                <span class="text-muted small">ยื่นเมื่อ: <?php echo $proposal['created_at'] ? date('d/m/Y', strtotime($proposal['created_at'])) : '-'; ?></span>
            </div>

            <?php if ($proposal['status'] === 'needs_revision' && !empty($proposal['revision_comment'])): ?>
            <div class="alert alert-danger border border-danger d-flex align-items-start gap-2 mb-3 p-2.5 shadow-sm small">
                <i class="ri-error-warning-line text-danger fs-5 leading-none"></i>
                <div>
                    <strong class="text-dark">สิ่งที่ควรปรับปรุงแก้ไขตามมติกรรมการ:</strong>
                    <div class="text-secondary whitespace-pre-wrap small mt-1"><?php echo htmlspecialchars($proposal['revision_comment']); ?></div>
                </div>
            </div>
            <?php endif; ?>
            
            <h5 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($proposal['title']); ?></h5>
            <p class="text-muted small mb-4"><em><?php echo htmlspecialchars($proposal['title_en'] ?? ''); ?></em></p>

            <div class="row g-3 bg-light p-3 rounded mb-4">
                <div class="col-md-6">
                    <label class="text-muted small d-block">หัวหน้าโครงการ</label>
                    <span class="fw-bold text-dark"><?php echo htmlspecialchars($proposal['leader_name'] ?? 'ไม่ระบุ'); ?></span>
                </div>
                <div class="col-md-6">
                    <label class="text-muted small d-block">ประเภทงานวิจัย</label>
                    <span class="fw-bold text-dark"><?php echo htmlspecialchars($proposal['research_type'] ?? 'ไม่ระบุ'); ?></span>
                </div>
                <div class="col-md-6">
                    <label class="text-muted small d-block">แหล่งทุนวิจัย</label>
                    <span class="fw-bold text-dark"><?php echo htmlspecialchars($proposal['funding_source_name'] ?? 'ไม่ระบุ'); ?></span>
                </div>
                <div class="col-md-6">
                    <label class="text-muted small d-block">งบประมาณเสนอขอ</label>
                    <span class="fw-bold text-success"><?php echo number_format($proposal['budget_total'] ?? 0, 2); ?> บาท</span>
                </div>
                <?php if ($proposal['project_id']): ?>
                <div class="col-md-12">
                    <label class="text-muted small d-block">รหัสโครงการหลังอนุมัติ</label>
                    <span class="badge bg-success"><i class="ri-folder-open-line me-1"></i> มีโครงการวิจัยแล้ว</span>
                </div>
                <?php endif; ?>
            </div>

            <h6 class="fw-bold text-dark mb-2">บทคัดย่อ (Abstract)</h6>
            <p class="text-secondary small leading-relaxed whitespace-pre-wrap mb-4" style="text-align: justify;">
                <?php echo htmlspecialchars($proposal['abstract'] ?? 'ไม่มีบทคัดย่อ'); ?>
            </p>

            <h6 class="fw-bold text-dark mb-2">ความเชื่อมโยงเชิงยุทธศาสตร์</h6>
            <p class="text-secondary small whitespace-pre-wrap mb-4"><?php echo htmlspecialchars($proposal['strategic_link'] ?? 'ไม่ได้ระบุ'); ?></p>

            <h6 class="fw-bold text-dark mb-2">ตัวชี้วัดผลกระทบ</h6>
            <p class="text-secondary small mb-0"><?php echo htmlspecialchars($proposal['impact_indicator'] ?? 'ไม่ได้ระบุ'); ?></p>
        </div>

        <!-- TAB 2: ทีมวิจัย -->
        <div class="tab-pane fade" id="mp-team-pane" role="tabpanel">
            <h6 class="fw-bold text-dark mb-3">ทีมงานวิจัย</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle small">
                    <thead class="table-light">
                        <tr>
                            <th>ชื่อ-นามสกุล</th>
                            <th>บทบาทหน้าที่</th>
                            <th class="text-center" style="width: 120px;">สัดส่วนภาระงาน</th>
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

        <!-- TAB 3: แผนงาน -->
        <div class="tab-pane fade" id="mp-milestones-pane" role="tabpanel">
            <h6 class="fw-bold text-dark mb-3">แผนดำเนินงานและเป้าหมายย่อย (Milestones)</h6>
            <?php 
            $milestones_array = [];
            if (!empty($proposal['milestones'])) {
                $milestones_array = json_decode($proposal['milestones'], true);
            }
            ?>
            <?php if (empty($milestones_array)): ?>
                <div class="text-center py-4 text-muted small">ไม่มีแผนการดำเนินงาน</div>
            <?php else: ?>
                <div class="list-group">
                    <?php foreach ($milestones_array as $index => $ms): ?>
                        <div class="list-group-item p-3">
                            <h6 class="fw-bold text-dark mb-1 small"><?php echo htmlspecialchars($ms['name'] ?? ''); ?></h6>
                            <p class="text-muted small mb-0"><?php echo htmlspecialchars($ms['description'] ?? ''); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- TAB 4: ผลประเมิน -->
        <?php if (!empty($reviews)): ?>
        <div class="tab-pane fade" id="mp-reviews-pane" role="tabpanel">
            <h6 class="fw-bold text-dark mb-3">ผลการประเมินจากกรรมการ</h6>
            <?php foreach ($reviews as $rev): ?>
                <div class="bg-light p-3 rounded mb-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                        <span class="fw-bold text-dark small">โดยกรรมการ: <?php echo htmlspecialchars($rev['reviewer_name']); ?></span>
                        <span class="badge bg-dark">คะแนนรวม: <?php echo $rev['total_score']; ?> / 100</span>
                    </div>
                    <div class="row g-2 mb-3 mt-1 small">
                        <div class="col-md-3"><strong>แนวคิด:</strong> <?php echo $rev['score_concept']; ?>/25</div>
                        <div class="col-md-3"><strong>ความพร้อมทีม:</strong> <?php echo $rev['score_team']; ?>/25</div>
                        <div class="col-md-3"><strong>ความสอดคล้อง:</strong> <?php echo $rev['score_alignment']; ?>/25</div>
                        <div class="col-md-3"><strong>ผลกระทบ:</strong> <?php echo $rev['score_impact']; ?>/25</div>
                    </div>
                    <?php if ($rev['comments_strengths']): ?>
                        <div class="mb-2">
                            <strong class="small text-muted d-block">จุดเด่น:</strong>
                            <span class="small text-dark"><?php echo htmlspecialchars($rev['comments_strengths']); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($rev['comments_suggestions']): ?>
                        <div>
                            <strong class="small text-muted d-block">ข้อเสนอแนะ:</strong>
                            <span class="small text-dark"><?php echo htmlspecialchars($rev['comments_suggestions']); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- TAB 5: เอกสารแนบ -->
        <div class="tab-pane fade" id="mp-files-pane" role="tabpanel">
            <h6 class="fw-bold text-dark mb-3">ไฟล์เอกสารแนบ</h6>
            <div class="list-group">
                <?php if ($proposal['file_proposal']): ?>
                <a href="../public/uploads/proposals/<?php echo htmlspecialchars($proposal['file_proposal']); ?>" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <span><i class="ri-file-pdf-line me-2 text-danger"></i> เอกสารข้อเสนอวิจัย</span>
                    <span class="text-primary small">ดาวน์โหลด</span>
                </a>
                <?php endif; ?>

                <?php if ($proposal['file_budget']): ?>
                <a href="../public/uploads/proposals/<?php echo htmlspecialchars($proposal['file_budget']); ?>" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <span><i class="ri-file-excel-line me-2 text-success"></i> เอกสารงบประมาณ</span>
                    <span class="text-primary small">ดาวน์โหลด</span>
                </a>
                <?php endif; ?>

                <?php if ($proposal['file_cv']): ?>
                <a href="../public/uploads/proposals/<?php echo htmlspecialchars($proposal['file_cv']); ?>" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <span><i class="ri-user-shared-line me-2 text-primary"></i> ประวัติหัวหน้าโครงการ (CV)</span>
                    <span class="text-primary small">ดาวน์โหลด</span>
                </a>
                <?php endif; ?>

                <?php if ($proposal['file_ethics']): ?>
                <a href="../public/uploads/proposals/<?php echo htmlspecialchars($proposal['file_ethics']); ?>" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <span><i class="ri-shield-check-line me-2 text-warning"></i> เอกสารรับรองจริยธรรม</span>
                    <span class="text-primary small">ดาวน์โหลด</span>
                </a>
                <?php endif; ?>

                <?php if (!$proposal['file_proposal'] && !$proposal['file_budget'] && !$proposal['file_cv'] && !$proposal['file_ethics']): ?>
                    <div class="text-center py-4 text-muted small">ไม่มีไฟล์เอกสารแนบ</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?php if (in_array($proposal['status'], ['draft', 'needs_revision']) && $proposal['user_id'] == $user_id): ?>
    <button onclick="openProposalModal(<?php echo $proposal['id']; ?>)" class="btn btn-dark fw-bold" data-bs-dismiss="modal">
        <i class="ri-edit-2-line me-1"></i> แก้ไขข้อเสนอโครงการ
    </button>
    <?php else: ?>
    <a href="proposal_detail.php?id=<?php echo $proposal['id']; ?>" class="btn btn-dark fw-bold">
        <i class="ri-external-link-line me-1"></i> ดูรายละเอียดแบบเต็มหน้าจอ
    </a>
    <?php endif; ?>
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
</div>
