<?php
// main/ajax/projects/GetTable.php
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');

if (!isLoggedIn()) { http_response_code(401); exit; }

$user_id   = authUser()['id'];
$isAdmin   = hasRole('admin');
$keyword   = trim($_POST['keyword'] ?? '');
$status    = trim($_POST['status'] ?? '');
$mode      = trim($_POST['mode'] ?? 'my');

// Build WHERE clauses
$params = [];
$whereClauses = ['1=1'];

if ($mode === 'my') {
    $whereClauses[] = 'pr.user_id = ?';
    $params[] = $user_id;
}

if ($keyword !== '') {
    $whereClauses[] = '(pr.title LIKE ? OR p.code LIKE ? OR u.name LIKE ?)';
    $like = '%' . $keyword . '%';
    $params[] = $like; $params[] = $like; $params[] = $like;
}

if ($status !== '') {
    $whereClauses[] = 'p.status = ?';
    $params[] = $status;
}

$whereSQL = implode(' AND ', $whereClauses);

// Fetch Projects - only actual projects (ongoing, completed, closed)
$projectRows = [];
$sql = "
    SELECT p.id, p.proposal_id, p.code, p.start_date, p.status as status_name,
           p.closure_requested, pr.title, pr.user_id, u.name as researcher_name, 'project' as item_type,
           COALESCE((SELECT (COUNT(CASE WHEN status = 'completed' THEN 1 END) * 100) / NULLIF(COUNT(*), 0) FROM project_milestones WHERE project_id = p.id), 0) as total_progress,
           (SELECT status FROM final_reports WHERE project_id = p.id LIMIT 1) as final_report_status
    FROM projects p
    LEFT JOIN proposals pr ON p.proposal_id = pr.id
    LEFT JOIN users u ON pr.user_id = u.id
    WHERE $whereSQL
    ORDER BY p.start_date DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projectRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// No standalone proposals are displayed on projects pages
$proposalRows = [];
$allItems = $projectRows;

// Status badge mapping
function getStatusBadge($row) {
    $statusRaw = strtolower($row['status_name'] ?? '');
    if ($row['closure_requested']) return ['label' => 'รอปิดโครงการ', 'color' => 'warning'];
    return match($statusRaw) {
        'ongoing', 'approved' => ['label' => 'กำลังดำเนินการ', 'color' => 'success'],
        'completed'           => ['label' => 'เสร็จสิ้น', 'color' => 'primary'],
        'closed'              => ['label' => 'ปิดโครงการ', 'color' => 'secondary'],
        default               => ['label' => $statusRaw, 'color' => 'light text-dark'],
    };
}
?>
<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle fs-6" id="projectsTable">
        <thead class="table-dark">
            <tr>
                <th>ชื่อโครงการ</th>
                <th>รหัส</th>
                <th>นักวิจัย</th>
                <th>สถานะ</th>
                <th>ความก้าวหน้า</th>
                <th>วันที่เริ่ม</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($allItems)): foreach ($allItems as $row):
            $badge = getStatusBadge($row);
            $progress = (int)($row['total_progress'] ?? 0);
            $detailId = $row['id'];
            $detailFunc = "showProjectDetail({$detailId})";
        ?>
            <tr>
                <td>
                    <a href="javascript:void(0)" onclick="<?php echo $detailFunc; ?>" class="fw-semibold text-decoration-none text-dark">
                        <?php echo htmlspecialchars($row['title'] ?? '-'); ?>
                    </a>
                </td>
                <td><code><?php echo htmlspecialchars($row['code'] ?? '-'); ?></code></td>
                <td><?php echo htmlspecialchars($row['researcher_name'] ?? '-'); ?></td>
                <td>
                    <span class="badge bg-<?php echo $badge['color']; ?>">
                        <?php echo $badge['label']; ?>
                    </span>
                </td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="height:8px; border-radius:4px; min-width:80px;">
                            <div class="progress-bar <?php echo $progress >= 100 ? 'bg-success' : 'bg-warning'; ?>"
                                 style="width:<?php echo min($progress,100); ?>%"></div>
                        </div>
                        <small class="fw-bold <?php echo $progress >= 100 ? 'text-success' : 'text-warning'; ?>">
                            <?php echo $progress; ?>%
                        </small>
                    </div>
                </td>
                <td><?php echo $row['start_date'] ? date('d/m/Y', strtotime($row['start_date'])) : '-'; ?></td>
            </tr>
        <?php endforeach; else: ?>
            <tr>
                <td colspan="6" class="text-center py-5 text-muted">
                    <i class="ri-folder-open-line" style="font-size:40px; display:block; margin-bottom:8px;"></i>
                    ไม่พบโครงการวิจัย
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
