<?php
// main/ajax/projects/DetailModal.php - รายละเอียดโครงการวิจัย (Modal)
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');

if (!isLoggedIn()) { http_response_code(401); exit; }

$project_id = (int)($_GET['id'] ?? 0);
if (!$project_id) { echo '<div class="alert alert-danger m-3">ไม่พบรหัสโครงการ</div>'; exit; }

$user_id = authUser()['id'];
$isAdmin = hasRole('admin');

// Fetch project data
$stmt = $pdo->prepare("
    SELECT p.*, p.status as project_status, 
           pr.budget_total, pr.title, pr.title_en, pr.research_type, pr.keywords, pr.abstract, pr.strategic_link, pr.impact_indicator,
           f.name as funding_source_name, u.name as leader_name,
           pr.user_id as owner_id,
           COALESCE((SELECT SUM(amount) FROM proposal_budget_items WHERE proposal_id = p.proposal_id), 0) as budget_used,
           COALESCE((SELECT (COUNT(CASE WHEN status = 'completed' THEN 1 END) * 100) / NULLIF(COUNT(*), 0) FROM project_milestones WHERE project_id = p.id), 0) as total_progress,
           (SELECT status FROM final_reports WHERE project_id = p.id LIMIT 1) as final_report_status
    FROM projects p
    LEFT JOIN proposals pr ON p.proposal_id = pr.id
    LEFT JOIN funding_sources f ON pr.funding_source_id = f.id
    LEFT JOIN users u ON pr.user_id = u.id
    WHERE p.id = ?
");
$stmt->execute([$project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) { echo '<div class="alert alert-danger m-3">ไม่พบข้อมูลโครงการ</div>'; exit; }

// Access check
if (!$isAdmin && $project['owner_id'] != $user_id) {
    echo '<div class="alert alert-danger m-3">คุณไม่มีสิทธิ์เข้าถึงโครงการนี้</div>';
    exit;
}

// Fetch milestones
$stmt = $pdo->prepare('SELECT * FROM project_milestones WHERE project_id = ? ORDER BY id ASC');
$stmt->execute([$project_id]);
$milestones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch progress reports
$stmt = $pdo->prepare('SELECT * FROM progress_reports WHERE project_id = ? ORDER BY created_at DESC');
$stmt->execute([$project_id]);
$progressReports = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch publications
$stmt = $pdo->prepare('SELECT * FROM publications WHERE project_id = ? ORDER BY publish_year DESC, created_at DESC');
$stmt->execute([$project_id]);
$publications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch IP assets
$stmt = $pdo->prepare('SELECT * FROM ip_creations WHERE project_id = ? ORDER BY created_at DESC');
$stmt->execute([$project_id]);
$ipAssets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch project files
$stmt = $pdo->prepare('SELECT * FROM project_files WHERE project_id = ? ORDER BY upload_date DESC');
$stmt->execute([$project_id]);
$projectFiles = $stmt->fetchAll(PDO::FETCH_ASSOC);

$statusClass = match(strtolower($project['project_status'] ?? '')) {
    'ongoing' => 'bg-success',
    'completed' => 'bg-primary',
    'closed' => 'bg-secondary',
    'terminated' => 'bg-danger',
    default => 'bg-dark'
};
?>
<div class="modal-header">
    <h5 class="modal-title fw-bold">
        <i class="ri-folder-open-line me-2"></i> รายละเอียดโครงการ (<?php echo htmlspecialchars($project['code'] ?? 'N/A'); ?>)
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body p-0">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs nav-fill border-bottom" id="modalProjectTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active py-2.5 fw-bold text-dark border-0 rounded-0" id="m-info-tab" data-bs-toggle="tab" data-bs-target="#m-info-pane" type="button" role="tab">
                ข้อมูลโครงการ
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link py-2.5 fw-bold text-dark border-0 rounded-0" id="m-milestones-tab" data-bs-toggle="tab" data-bs-target="#m-milestones-pane" type="button" role="tab">
                แผนงาน (<?php echo count($milestones); ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link py-2.5 fw-bold text-dark border-0 rounded-0" id="m-progress-tab" data-bs-toggle="tab" data-bs-target="#m-progress-pane" type="button" role="tab">
                ความก้าวหน้า (<?php echo count($progressReports); ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link py-2.5 fw-bold text-dark border-0 rounded-0" id="m-outputs-tab" data-bs-toggle="tab" data-bs-target="#m-outputs-pane" type="button" role="tab">
                ผลงาน (<?php echo count($publications) + count($ipAssets); ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link py-2.5 fw-bold text-dark border-0 rounded-0" id="m-files-tab" data-bs-toggle="tab" data-bs-target="#m-files-pane" type="button" role="tab">
                เอกสาร (<?php echo count($projectFiles); ?>)
            </button>
        </li>
    </ul>

    <div class="tab-content p-4" id="modalProjectTabContent" style="max-height: 60vh; overflow-y: auto;">
        <!-- TAB 1: ข้อมูลทั่วไป -->
        <div class="tab-pane fade show active" id="m-info-pane" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge <?php echo $statusClass; ?> px-2.5 py-1.5 fs-6 rounded-pill">
                    <?php echo match(strtolower($project['project_status'] ?? '')) {
                        'ongoing' => 'กำลังดำเนินการ',
                        'completed' => 'เสร็จสิ้นโครงการ',
                        'closed' => 'ปิดโครงการแล้ว',
                        'terminated' => 'ยกเลิกโครงการ',
                        default => $project['project_status']
                    }; ?>
                </span>
                <span class="fw-bold text-success">ความก้าวหน้า: <?php echo (int)$project['total_progress']; ?>%</span>
            </div>
            
            <h5 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($project['title']); ?></h5>
            <p class="text-muted small mb-4"><em><?php echo htmlspecialchars($project['title_en'] ?? ''); ?></em></p>

            <div class="row g-3 bg-light p-3 rounded mb-4">
                <div class="col-md-6">
                    <label class="text-muted small d-block">หัวหน้าโครงการ</label>
                    <span class="fw-bold text-dark"><?php echo htmlspecialchars($project['leader_name'] ?? 'ไม่ระบุ'); ?></span>
                </div>
                <div class="col-md-6">
                    <label class="text-muted small d-block">ประเภทงานวิจัย</label>
                    <span class="fw-bold text-dark"><?php echo htmlspecialchars($project['research_type'] ?? 'ไม่ระบุ'); ?></span>
                </div>
                <div class="col-md-6">
                    <label class="text-muted small d-block">แหล่งทุนวิจัย</label>
                    <span class="fw-bold text-dark"><?php echo htmlspecialchars($project['funding_source_name'] ?? 'ไม่ระบุ'); ?></span>
                </div>
                <div class="col-md-6">
                    <label class="text-muted small d-block">งบประมาณรวม</label>
                    <span class="fw-bold text-success"><?php echo number_format($project['budget_total'] ?? 0, 2); ?> บาท</span>
                </div>
                <div class="col-md-12">
                    <label class="text-muted small d-block">ระยะเวลาโครงการ</label>
                    <span class="fw-bold text-dark">
                        <?php echo $project['start_date'] ? date('d/m/Y', strtotime($project['start_date'])) : '-'; ?> ถึง 
                        <?php echo $project['end_date'] ? date('d/m/Y', strtotime($project['end_date'])) : '-'; ?>
                    </span>
                </div>
            </div>

            <h6 class="fw-bold text-dark mb-2">บทคัดย่อ (Abstract)</h6>
            <p class="text-secondary small leading-relaxed whitespace-pre-wrap mb-4" style="text-align: justify;">
                <?php echo htmlspecialchars($project['abstract'] ?? 'ไม่มีบทคัดย่อ'); ?>
            </p>
        </div>

        <!-- TAB 2: แผนงาน -->
        <div class="tab-pane fade" id="m-milestones-pane" role="tabpanel">
            <h6 class="fw-bold text-dark mb-3">แผนการดำเนินงาน (Milestones)</h6>
            <?php if (empty($milestones)): ?>
                <div class="text-center py-4 text-muted small">ไม่มีแผนงานย่อย</div>
            <?php else: ?>
                <div class="list-group">
                    <?php foreach ($milestones as $ms): ?>
                        <div class="list-group-item p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-dark"><?php echo htmlspecialchars($ms['milestone_name']); ?></span>
                                <span class="badge bg-<?php echo $ms['status'] === 'completed' ? 'success' : ($ms['status'] === 'in_progress' ? 'warning text-dark' : 'secondary'); ?>"><?php echo $ms['status']; ?></span>
                            </div>
                            <p class="text-muted small mb-0"><?php echo htmlspecialchars($ms['description'] ?? 'ไม่มีคำอธิบาย'); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- TAB 3: ความก้าวหน้า -->
        <div class="tab-pane fade" id="m-progress-pane" role="tabpanel">
            <h6 class="fw-bold text-dark mb-3">รายงานความก้าวหน้าโครงการ</h6>
            <?php if (empty($progressReports)): ?>
                <div class="text-center py-4 text-muted small">ยังไม่มีการส่งรายงานความก้าวหน้า</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle small">
                        <thead class="table-light">
                            <tr>
                                <th>งวด</th>
                                <th>ความคืบหน้า</th>
                                <th>สถานะการเงิน</th>
                                <th>ความเสี่ยง</th>
                                <th>วันที่ส่ง</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($progressReports as $r): ?>
                                <tr>
                                    <td class="fw-bold"><?php echo htmlspecialchars($r['report_period']); ?></td>
                                    <td class="fw-bold text-success"><?php echo $r['percentage_complete']; ?>%</td>
                                    <td><?php echo htmlspecialchars($r['budget_spending_status'] ?? '-'); ?></td>
                                    <td><span class="badge bg-<?php echo ($r['risk_level'] === 'High') ? 'danger' : (($r['risk_level'] === 'Medium') ? 'warning text-dark' : 'success'); ?>"><?php echo $r['risk_level'] ?? 'Low'; ?></span></td>
                                    <td><?php echo date('d/m/Y', strtotime($r['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- TAB 4: ผลงาน -->
        <div class="tab-pane fade" id="m-outputs-pane" role="tabpanel">
            <h6 class="fw-bold text-dark mb-3">ผลงานตีพิมพ์ (Publications)</h6>
            <?php if (empty($publications)): ?>
                <p class="text-muted italic small py-2">ไม่มีข้อมูลผลงานตีพิมพ์</p>
            <?php else: ?>
                <div class="list-group mb-4">
                    <?php foreach ($publications as $pub): ?>
                        <div class="list-group-item p-3">
                            <h6 class="mb-1 fw-bold text-dark small"><?php echo htmlspecialchars($pub['title_th']); ?> (<?php echo htmlspecialchars($pub['publish_year']); ?>)</h6>
                            <p class="mb-1 small text-muted"><?php echo htmlspecialchars($pub['journal_name'] ?? 'ไม่ระบุวารสาร'); ?></p>
                            <span class="badge bg-secondary"><?php echo htmlspecialchars($pub['publication_type']); ?></span>
                            <?php if ($pub['quartile']): ?><span class="badge bg-warning text-dark"><?php echo htmlspecialchars($pub['quartile']); ?></span><?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <h6 class="fw-bold text-dark mb-3">ทรัพย์สินทางปัญญา (IP Assets)</h6>
            <?php if (empty($ipAssets)): ?>
                <p class="text-muted italic small py-2">ไม่มีข้อมูลทรัพย์สินทางปัญญา</p>
            <?php else: ?>
                <div class="list-group">
                    <?php foreach ($ipAssets as $ip): ?>
                        <div class="list-group-item p-3">
                            <h6 class="mb-1 fw-bold text-dark small"><?php echo htmlspecialchars($ip['name_th']); ?></h6>
                            <p class="mb-1 small text-muted">หมายเลขทะเบียน: <?php echo htmlspecialchars($ip['registration_number'] ?? 'อยู่ระหว่างการดำเนินการ'); ?></p>
                            <span class="badge bg-dark"><?php echo htmlspecialchars($ip['ip_type']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- TAB 5: เอกสาร -->
        <div class="tab-pane fade" id="m-files-pane" role="tabpanel">
            <h6 class="fw-bold text-dark mb-3">เอกสารแนบโครงการ</h6>
            <?php if (empty($projectFiles)): ?>
                <div class="text-center py-4 text-muted small">ยังไม่มีการอัปโหลดไฟล์เอกสาร</div>
            <?php else: ?>
                <div class="list-group">
                    <?php foreach ($projectFiles as $file): ?>
                        <a href="../public/<?php echo htmlspecialchars($file['file_path']); ?>" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="ri-file-line me-2"></i> <?php echo htmlspecialchars($file['file_type']); ?></span>
                            <span class="text-muted small"><?php echo date('d/m/Y', strtotime($file['upload_date'])); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="modal-footer">
    <a href="project_detail.php?id=<?php echo $project_id; ?>" class="btn btn-dark fw-bold">
        <i class="ri-external-link-line me-1"></i> จัดการโครงการแบบเต็มหน้าจอ
    </a>
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
</div>
