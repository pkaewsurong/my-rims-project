<?php
// main/project_detail.php - รายละเอียดโครงการวิจัย
$pageTitle = 'รายละเอียดโครงการ';
$pageCss   = 'project_detail';
$pageJs    = 'project_detail';
require 'header.php';

$project_id = (int)($_GET['id'] ?? 0);
if (!$project_id) {
    echo '<div class="alert alert-danger">ไม่พบรหัสโครงการ</div>';
    require 'footer.php';
    exit;
}

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

if (!$project) {
    echo '<div class="alert alert-danger">ไม่พบข้อมูลโครงการ</div>';
    require 'footer.php';
    exit;
}

// Access check
if (!$isAdmin && $project['owner_id'] != $user_id) {
    echo '<div class="alert alert-danger">คุณไม่มีสิทธิ์เข้าถึงโครงการนี้</div>';
    require 'footer.php';
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

// Fetch final report
$stmt = $pdo->prepare('SELECT * FROM final_reports WHERE project_id = ? LIMIT 1');
$stmt->execute([$project_id]);
$finalReport = $stmt->fetch(PDO::FETCH_ASSOC);

$statusClass = match(strtolower($project['project_status'] ?? '')) {
    'ongoing' => 'bg-success text-white',
    'completed' => 'bg-primary text-white',
    'closed' => 'bg-secondary text-white',
    'terminated' => 'bg-danger text-white',
    default => 'bg-dark text-white'
};
?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-4">
    <div>
        <h1 class="page-title"><i class="ri-folder-open-line me-2"></i>รายละเอียดโครงการ #<?php echo htmlspecialchars($project['code'] ?? 'N/A'); ?></h1>
        <nav aria-label="breadcrumb" class="mt-1">
            <ol class="breadcrumb mb-0" style="font-size:13px;">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">หน้าหลัก</a></li>
                <li class="breadcrumb-item"><a href="projects.php" class="text-decoration-none">โครงการของฉัน</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($project['code'] ?? 'N/A'); ?></li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <?php if ($isAdmin && ($project['closure_requested'] ?? false)): ?>
        <button onclick="approveClosure(<?php echo $project['id']; ?>)" class="btn btn-warning fw-bold">
            <i class="ri-checkbox-circle-line me-1"></i> อนุมัติการปิดโครงการ
        </button>
        <?php endif; ?>
        <a href="projects.php" class="btn btn-outline-secondary">
            <i class="ri-arrow-left-line me-1"></i> กลับหน้ารวม
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Left column: Main Tabs -->
    <div class="col-lg-9">
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white p-0">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-fill border-bottom-0" id="projectTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active py-3 fw-bold text-dark border-0" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-pane" type="button" role="tab" aria-selected="true">
                            <i class="ri-information-line me-1"></i> ข้อมูลโครงการ
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 fw-bold text-dark border-0" id="milestones-tab" data-bs-toggle="tab" data-bs-target="#milestones-pane" type="button" role="tab" aria-selected="false">
                            <i class="ri-flag-line me-1"></i> แผนดำเนินงาน (<?php echo count($milestones); ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 fw-bold text-dark border-0" id="progress-tab" data-bs-toggle="tab" data-bs-target="#progress-pane" type="button" role="tab" aria-selected="false">
                            <i class="ri-line-chart-line me-1"></i> ความก้าวหน้า (<?php echo count($progressReports); ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 fw-bold text-dark border-0" id="outputs-tab" data-bs-toggle="tab" data-bs-target="#outputs-pane" type="button" role="tab" aria-selected="false">
                            <i class="ri-award-line me-1"></i> ผลงาน & IP (<?php echo count($publications) + count($ipAssets); ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 fw-bold text-dark border-0" id="files-tab" data-bs-toggle="tab" data-bs-target="#files-pane" type="button" role="tab" aria-selected="false">
                            <i class="ri-attachment-line me-1"></i> เอกสารแนบ (<?php echo count($projectFiles); ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 fw-bold text-dark border-0" id="final-tab" data-bs-toggle="tab" data-bs-target="#final-pane" type="button" role="tab" aria-selected="false">
                            <i class="ri-checkbox-circle-line me-1"></i> รายงานสรุป
                        </button>
                    </li>
                </ul>
            </div>
            
            <div class="card-body p-4">
                <div class="tab-content" id="projectTabContent">
                    
                    <!-- TAB 1: ข้อมูลโครงการ -->
                    <div class="tab-pane fade show active" id="info-pane" role="tabpanel" aria-labelledby="info-tab">
                        <div class="mb-4">
                            <h4 class="fw-bold text-dark mb-2"><?php echo htmlspecialchars($project['title']); ?></h4>
                            <p class="text-muted fs-6 mb-3"><em><?php echo htmlspecialchars($project['title_en'] ?? ''); ?></em></p>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-muted small d-block">หัวหน้าโครงการ</label>
                                <span class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($project['leader_name'] ?? 'ไม่ระบุ'); ?></span>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small d-block">ประเภทงานวิจัย</label>
                                <span class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($project['research_type'] ?? 'ไม่ระบุ'); ?></span>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small d-block">แหล่งทุนวิจัย</label>
                                <span class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($project['funding_source_name'] ?? 'ไม่ระบุ'); ?></span>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small d-block">งบประมาณรวม</label>
                                <span class="fw-bold text-success fs-6"><?php echo number_format($project['budget_total'] ?? 0, 2); ?> บาท</span>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small d-block">ระยะเวลาโครงการ</label>
                                <span class="fw-bold text-dark fs-6">
                                    <i class="ri-calendar-line text-muted"></i>
                                    <?php echo $project['start_date'] ? date('d M Y', strtotime($project['start_date'])) : '-'; ?> ถึง 
                                    <?php echo $project['end_date'] ? date('d M Y', strtotime($project['end_date'])) : '-'; ?>
                                </span>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small d-block">คำสำคัญ (Keywords)</label>
                                <span class="text-dark fs-6"><?php echo htmlspecialchars($project['keywords'] ?? '-'); ?></span>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-2"><i class="ri-file-list-3-line text-muted me-1"></i> บทคัดย่อ (Abstract)</h5>
                            <p class="text-secondary leading-relaxed whitespace-pre-wrap" style="text-align: justify;">
                                <?php echo htmlspecialchars($project['abstract'] ?? 'ไม่มีบทคัดย่อ'); ?>
                            </p>
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-2"><i class="ri-focus-3-line text-muted me-1"></i> ความเชื่อมโยงกับยุทธศาสตร์</h5>
                            <p class="text-secondary whitespace-pre-wrap"><?php echo htmlspecialchars($project['strategic_link'] ?? 'ไม่มีความเชื่อมโยง'); ?></p>
                        </div>

                        <div>
                            <h5 class="fw-bold text-dark mb-2"><i class="ri-line-chart-line text-muted me-1"></i> ตัวชี้วัดผลกระทบ</h5>
                            <p class="text-secondary"><?php echo htmlspecialchars($project['impact_indicator'] ?? 'ไม่มีข้อมูลตัวชี้วัด'); ?></p>
                        </div>
                    </div>

                    <!-- TAB 2: แผนงาน / เป้าหมาย -->
                    <div class="tab-pane fade" id="milestones-pane" role="tabpanel" aria-labelledby="milestones-tab">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold text-dark mb-0"><i class="ri-flag-line text-muted me-1"></i> แผนการดำเนินงานและเป้าหมายย่อย (Milestones)</h5>
                            <?php if (!$project['closure_requested'] && $project['project_status'] !== 'closed'): ?>
                            <button onclick="addMilestone(<?php echo $project['id']; ?>)" class="btn btn-sm btn-dark">
                                <i class="ri-add-line me-1"></i> เพิ่มแผนงานย่อย
                            </button>
                            <?php endif; ?>
                        </div>

                        <?php if (empty($milestones)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="ri-flag-line fs-1 d-block mb-2 text-secondary"></i>
                                ไม่มีแผนงานย่อย
                            </div>
                        <?php else: ?>
                            <div class="timeline-container">
                                <?php foreach ($milestones as $ms): ?>
                                    <div class="card mb-3 border border-slate-100 shadow-none">
                                        <div class="card-body py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <div>
                                                <h6 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($ms['milestone_name']); ?></h6>
                                                <p class="text-muted small mb-0"><?php echo htmlspecialchars($ms['description'] ?? 'ไม่มีคำอธิบาย'); ?></p>
                                            </div>
                                            <div class="d-flex align-items-center gap-3">
                                                <select onchange="updateMilestoneStatus(<?php echo $ms['id']; ?>, this.value)" class="form-select form-select-sm" style="width: 140px;" <?php echo ($project['project_status'] === 'closed') ? 'disabled' : ''; ?>>
                                                    <option value="pending" <?php echo $ms['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="in_progress" <?php echo $ms['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                                    <option value="completed" <?php echo $ms['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                </select>
                                                <?php if (!$project['closure_requested'] && $project['project_status'] !== 'closed'): ?>
                                                <button onclick="deleteMilestone(<?php echo $ms['id']; ?>)" class="btn btn-sm btn-outline-danger border-0">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TAB 3: รายงานความก้าวหน้า -->
                    <div class="tab-pane fade" id="progress-pane" role="tabpanel" aria-labelledby="progress-tab">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold text-dark mb-0"><i class="ri-line-chart-line text-muted me-1"></i> รายงานความก้าวหน้าโครงการ (Progress Reports)</h5>
                            <?php if (!$project['closure_requested'] && $project['project_status'] !== 'closed'): ?>
                            <button onclick="openProgressModal(<?php echo $project['id']; ?>)" class="btn btn-sm btn-dark">
                                <i class="ri-add-line me-1"></i> ส่งรายงานความก้าวหน้า
                            </button>
                            <?php endif; ?>
                        </div>

                        <?php if (empty($progressReports)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="ri-bubble-chart-line fs-1 d-block mb-2 text-secondary"></i>
                                ยังไม่มีการส่งรายงานความก้าวหน้า
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 80px;">งวด</th>
                                            <th>ความคืบหน้า</th>
                                            <th>สถานะการเงิน</th>
                                            <th>ความเสี่ยง</th>
                                            <th>วันที่ยื่นรายงาน</th>
                                            <th style="width: 120px;" class="text-center">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($progressReports as $r): ?>
                                            <tr>
                                                <td class="fw-bold"><?php echo htmlspecialchars($r['report_period']); ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="progress flex-grow-1" style="height: 6px;">
                                                            <div class="progress-bar bg-success" style="width: <?php echo $r['percentage_complete']; ?>%;"></div>
                                                        </div>
                                                        <span class="small fw-bold text-success"><?php echo $r['percentage_complete']; ?>%</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php
                                                    $spending = $r['budget_spending_status'] ?? 'ตามแผน';
                                                    $spColor = str_contains($spending, 'ตามแผน') ? 'success' : (str_contains($spending, 'ล่าช้า') ? 'warning' : 'danger');
                                                    ?>
                                                    <span class="badge bg-<?php echo $spColor; ?>"><?php echo htmlspecialchars($spending); ?></span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $risk = $r['risk_level'] ?? 'Low';
                                                    $riskColor = match($risk) {
                                                        'High' => 'danger',
                                                        'Medium' => 'warning text-dark',
                                                        default => 'success'
                                                    };
                                                    ?>
                                                    <span class="badge bg-<?php echo $riskColor; ?>"><?php echo $risk; ?></span>
                                                </td>
                                                <td><?php echo date('d M Y H:i', strtotime($r['created_at'])); ?></td>
                                                <td class="text-center">
                                                    <button onclick="viewProgressDetail(<?php echo $r['id']; ?>)" class="btn btn-sm btn-outline-dark" title="ดูรายละเอียด">
                                                        <i class="ri-eye-line"></i>
                                                    </button>
                                                    <?php if (!$project['closure_requested'] && $project['project_status'] !== 'closed'): ?>
                                                    <button onclick="deleteProgressReport(<?php echo $r['id']; ?>)" class="btn btn-sm btn-outline-danger ms-1" title="ลบ">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TAB 4: ผลงาน & IP -->
                    <div class="tab-pane fade" id="outputs-pane" role="tabpanel" aria-labelledby="outputs-tab">
                        <!-- Publications Sub-section -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-dark mb-0"><i class="ri-article-line text-muted me-1"></i> ผลงานตีพิมพ์ (Publications)</h5>
                            <?php if (!$project['closure_requested'] && $project['project_status'] !== 'closed'): ?>
                            <button onclick="openOutputModal(<?php echo $project['id']; ?>)" class="btn btn-sm btn-outline-dark">
                                <i class="ri-add-line me-1"></i> เพิ่มผลงานตีพิมพ์
                            </button>
                            <?php endif; ?>
                        </div>

                        <?php if (empty($publications)): ?>
                            <p class="text-muted italic small py-2 mb-4 border-bottom pb-4">ไม่มีข้อมูลผลงานตีพิมพ์</p>
                        <?php else: ?>
                            <div class="list-group mb-4">
                                <?php foreach ($publications as $pub): ?>
                                    <div class="list-group-item list-group-item-action p-3">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1 fw-bold text-dark"><?php echo htmlspecialchars($pub['title_th']); ?></h6>
                                            <small class="text-muted"><?php echo htmlspecialchars($pub['publish_year']); ?></small>
                                        </div>
                                        <p class="mb-1 small text-muted"><?php echo htmlspecialchars($pub['journal_name'] ?? 'ไม่ระบุวารสาร'); ?></p>
                                        <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap gap-2">
                                            <div class="d-flex gap-2">
                                                <span class="badge bg-secondary"><?php echo htmlspecialchars($pub['publication_type']); ?></span>
                                                <?php if ($pub['indexing_database']): ?>
                                                <span class="badge bg-info text-dark"><?php echo htmlspecialchars($pub['indexing_database']); ?></span>
                                                <?php endif; ?>
                                                <?php if ($pub['quartile']): ?>
                                                <span class="badge bg-warning text-dark"><?php echo htmlspecialchars($pub['quartile']); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="d-flex gap-1">
                                                <?php if ($pub['doi_url']): ?>
                                                <a href="<?php echo htmlspecialchars($pub['doi_url']); ?>" target="_blank" class="btn btn-xs btn-outline-secondary py-0 px-2 small">DOI</a>
                                                <?php endif; ?>
                                                <?php if ($pub['file_full_text']): ?>
                                                <a href="../public/uploads/publications/<?php echo htmlspecialchars($pub['file_full_text']); ?>" target="_blank" class="btn btn-xs btn-outline-success py-0 px-2 small"><i class="ri-file-pdf-line"></i> Full Text</a>
                                                <?php endif; ?>
                                                <?php if (!$project['closure_requested'] && $project['project_status'] !== 'closed'): ?>
                                                <button onclick="deletePublication(<?php echo $pub['id']; ?>)" class="btn btn-xs btn-outline-danger py-0 px-2 small border-0"><i class="ri-delete-bin-line"></i></button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- IP Creations Sub-section -->
                        <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                            <h5 class="fw-bold text-dark mb-0"><i class="ri-copyright-line text-muted me-1"></i> ทรัพย์สินทางปัญญา (IP Assets)</h5>
                            <?php if (!$project['closure_requested'] && $project['project_status'] !== 'closed'): ?>
                            <button onclick="openIPModal(<?php echo $project['id']; ?>)" class="btn btn-sm btn-outline-dark">
                                <i class="ri-add-line me-1"></i> จดทะเบียนทรัพย์สินทางปัญญา
                            </button>
                            <?php endif; ?>
                        </div>

                        <?php if (empty($ipAssets)): ?>
                            <p class="text-muted italic small py-2">ไม่มีข้อมูลการจดทะเบียนทรัพย์สินทางปัญญา</p>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($ipAssets as $ip): ?>
                                    <div class="list-group-item list-group-item-action p-3">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1 fw-bold text-dark"><?php echo htmlspecialchars($ip['name_th']); ?></h6>
                                            <small class="text-muted"><?php echo $ip['completion_date'] ? date('d/m/Y', strtotime($ip['completion_date'])) : ''; ?></small>
                                        </div>
                                        <p class="mb-1 small text-muted">หมายเลขทะเบียน: <?php echo htmlspecialchars($ip['registration_number'] ?? 'อยู่ระหว่างการดำเนินการ'); ?></p>
                                        <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap gap-2">
                                            <div class="d-flex gap-2">
                                                <span class="badge bg-dark"><?php echo htmlspecialchars($ip['ip_type']); ?></span>
                                                <span class="badge bg-secondary"><?php echo htmlspecialchars($ip['legal_status'] ?? 'ยื่นคำขอ'); ?></span>
                                            </div>
                                            <div class="d-flex gap-1">
                                                <?php if ($ip['file_certificate']): ?>
                                                <a href="../public/uploads/ip/<?php echo htmlspecialchars($ip['file_certificate']); ?>" target="_blank" class="btn btn-xs btn-outline-success py-0 px-2"><i class="ri-file-pdf-line"></i> Certificate</a>
                                                <?php endif; ?>
                                                <?php if (!$project['closure_requested'] && $project['project_status'] !== 'closed'): ?>
                                                <button onclick="deleteIP(<?php echo $ip['id']; ?>)" class="btn btn-xs btn-outline-danger py-0 px-2 border-0"><i class="ri-delete-bin-line"></i></button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TAB 5: เอกสารแนบ -->
                    <div class="tab-pane fade" id="files-pane" role="tabpanel" aria-labelledby="files-tab">
                        <h5 class="fw-bold text-dark mb-4"><i class="ri-attachment-line text-muted me-1"></i> ไฟล์เอกสารโครงการ (Project Files)</h5>

                        <?php if (!$project['closure_requested'] && $project['project_status'] !== 'closed'): ?>
                        <form id="fileUploadForm" class="bg-light p-3 rounded border border-slate-100 mb-4">
                            <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-6 col-sm-12">
                                    <label class="form-label small fw-bold">ชื่อไฟล์/คำอธิบายเอกสาร <span class="text-danger">*</span></label>
                                    <input type="text" name="file_type" class="form-control form-control-sm" required placeholder="เช่น สัญญาโครงการ, ใบเสนอราคา">
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <label class="form-label small fw-bold">เลือกไฟล์ <span class="text-danger">*</span></label>
                                    <input type="file" name="file_path" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-2 col-sm-12">
                                    <button type="submit" class="btn btn-dark btn-sm w-100"><i class="ri-upload-line me-1"></i> อัปโหลด</button>
                                </div>
                            </div>
                        </form>
                        <?php endif; ?>

                        <div id="projectFilesTableContainer">
                            <?php if (empty($projectFiles)): ?>
                                <div class="text-center py-5 text-muted">
                                    <i class="ri-folder-zip-line fs-1 d-block mb-2 text-secondary"></i>
                                    ยังไม่มีเอกสารที่อัปโหลด
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ชื่อไฟล์ / คำอธิบาย</th>
                                                <th>วันที่อัปโหลด</th>
                                                <th style="width: 150px;" class="text-center">ดาวน์โหลด / ลบ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($projectFiles as $file): ?>
                                                <tr>
                                                    <td class="fw-semibold">
                                                        <i class="ri-file-line text-secondary me-1"></i>
                                                        <?php echo htmlspecialchars($file['file_type']); ?>
                                                    </td>
                                                    <td><?php echo date('d M Y H:i', strtotime($file['upload_date'])); ?></td>
                                                    <td class="text-center">
                                                        <a href="../public/<?php echo htmlspecialchars($file['file_path']); ?>" target="_blank" class="btn btn-sm btn-outline-dark" title="ดาวน์โหลด">
                                                            <i class="ri-download-line"></i>
                                                        </a>
                                                        <?php if (!$project['closure_requested'] && $project['project_status'] !== 'closed'): ?>
                                                        <button onclick="deleteProjectFile(<?php echo $file['id']; ?>)" class="btn btn-sm btn-outline-danger ms-1" title="ลบไฟล์">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- TAB 6: รายงานฉบับสมบูรณ์ (Final Report) -->
                    <div class="tab-pane fade" id="final-pane" role="tabpanel" aria-labelledby="final-tab">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold text-dark mb-0"><i class="ri-checkbox-circle-line text-muted me-1"></i> รายงานฉบับสมบูรณ์ (Final Report)</h5>
                            <?php if (!$finalReport && !$project['closure_requested'] && $project['project_status'] !== 'closed'): ?>
                            <button onclick="openFinalReportModal(<?php echo $project['id']; ?>)" class="btn btn-sm btn-dark" <?php echo ($project['total_progress'] < 100) ? 'disabled title="กรุณาส่งความก้าวหน้าให้ครบ 100% ก่อน"' : ''; ?>>
                                <i class="ri-send-plane-line me-1"></i> ยื่นรายงานฉบับสมบูรณ์
                            </button>
                            <?php endif; ?>
                        </div>

                        <?php if ($project['total_progress'] < 100): ?>
                            <div class="alert alert-warning">
                                <i class="ri-error-warning-line me-2"></i>
                                ความก้าวหน้ารวมของโครงการยังไม่ถึง 100% (ปัจจุบัน: <?php echo $project['total_progress']; ?>%) ไม่สามารถส่งรายงานฉบับสมบูรณ์ได้
                            </div>
                        <?php endif; ?>

                        <?php if (!$finalReport): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="ri-survey-line fs-1 d-block mb-2 text-secondary"></i>
                                ยังไม่มีการส่งรายงานฉบับสมบูรณ์
                            </div>
                        <?php else: ?>
                            <div class="card p-3 mb-4 bg-light border-0">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="text-muted small d-block">สถานะรายงาน</label>
                                        <span class="badge bg-<?php echo ($finalReport['status'] === 'approved') ? 'success' : (($finalReport['status'] === 'submitted') ? 'warning text-dark' : 'secondary'); ?> fs-6 mt-1">
                                            <?php echo htmlspecialchars($finalReport['status'] === 'approved' ? 'อนุมัติแล้ว' : ($finalReport['status'] === 'submitted' ? 'รอพิจารณา' : 'แบบร่าง')); ?>
                                        </span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small d-block">วันที่ยื่นรายงาน</label>
                                        <span class="fw-bold text-dark"><?php echo date('d M Y', strtotime($finalReport['submission_date'])); ?></span>
                                    </div>
                                    <div class="col-12 mt-3">
                                        <label class="text-muted small d-block">บทสรุปผู้บริหาร (Executive Summary)</label>
                                        <p class="text-dark bg-white p-3 border rounded whitespace-pre-wrap mt-1"><?php echo htmlspecialchars($finalReport['executive_summary'] ?? 'ไม่มีข้อมูล'); ?></p>
                                    </div>
                                    <div class="col-12">
                                        <label class="text-muted small d-block">ประโยชน์ของโครงการวิจัยและการนำไปใช้ประโยชน์</label>
                                        <p class="text-dark bg-white p-3 border rounded whitespace-pre-wrap mt-1"><?php echo htmlspecialchars($finalReport['utilization_impact'] ?? 'ไม่มีข้อมูล'); ?></p>
                                    </div>
                                    <?php if ($finalReport['file_report_pdf']): ?>
                                    <div class="col-12 mt-3">
                                        <label class="text-muted small d-block">ไฟล์รายงานวิจัยฉบับสมบูรณ์ (PDF)</label>
                                        <a href="../public/uploads/final_reports/<?php echo htmlspecialchars($finalReport['file_report_pdf']); ?>" target="_blank" class="btn btn-outline-success btn-sm mt-2">
                                            <i class="ri-file-pdf-line me-1"></i> ดาวน์โหลดรายงานวิจัยฉบับสมบูรณ์
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
    
    <!-- Right column: Project Overview Card -->
    <div class="col-lg-3">
        <div class="card mb-4 shadow-sm border-0" style="border-top: 4px solid #b9ff66 !important;">
            <div class="card-body">
                <h5 class="fw-bold text-dark mb-4">สถานะโครงการ</h5>
                
                <div class="text-center py-2 mb-4">
                    <span class="badge <?php echo $statusClass; ?> px-3 py-2 fs-6 rounded-pill" style="min-width: 120px;">
                        <?php echo match(strtolower($project['project_status'] ?? '')) {
                            'ongoing' => 'กำลังดำเนินการ',
                            'completed' => 'เสร็จสิ้นโครงการ',
                            'closed' => 'ปิดโครงการแล้ว',
                            'terminated' => 'ยกเลิกโครงการ',
                            default => $project['project_status']
                        }; ?>
                    </span>
                    <?php if ($project['closure_requested']): ?>
                    <div class="text-warning small mt-2 fw-bold"><i class="ri-error-warning-line"></i> ส่งคำขอปิดโครงการแล้ว</div>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <label class="text-muted small d-block mb-1">ความก้าวหน้าโครงการ</label>
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="height: 10px; border-radius: 5px;">
                            <div class="progress-bar bg-success" style="width: <?php echo min((int)$project['total_progress'], 100); ?>%;"></div>
                        </div>
                        <span class="fw-bold text-success fs-6"><?php echo (int)$project['total_progress']; ?>%</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="text-muted small d-block mb-1">งบประมาณการใช้งวดงาน</label>
                    <span class="fw-bold text-dark fs-5"><?php echo number_format($project['budget_used'], 2); ?> ฿</span>
                    <small class="text-muted d-block">จากงบประมาณรวม <?php echo number_format($project['budget_total'], 0); ?> ฿</small>
                </div>

                <?php if (!$project['closure_requested'] && $project['project_status'] === 'ongoing' && (int)$project['total_progress'] >= 100): ?>
                <hr>
                <button onclick="requestProjectClosure(<?php echo $project['id']; ?>, <?php echo (int)$project['total_progress']; ?>, <?php echo $finalReport ? 'true' : 'false'; ?>)" class="btn btn-warning fw-bold w-100 text-dark">
                    <i class="ri-close-circle-line me-1"></i> ยื่นเรื่องขอปิดโครงการ
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Main Modal สำหรับ AJAX forms -->
<div class="modal fade" id="mainModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" id="showModal"></div>
    </div>
</div>

<?php require 'footer.php'; ?>
