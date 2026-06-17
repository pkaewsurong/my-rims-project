<?php
// main/ajax/dashboard/GetTable.php - Recent Projects table
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');
if (!isLoggedIn()) { http_response_code(401); exit; }

$user_id = authUser()['id'];
$isAdmin = hasRole('admin');

if ($isAdmin) {
    $stmt = $pdo->prepare("
        SELECT p.id, p.code, p.start_date, p.status, pr.title, u.name as researcher_name,
               COALESCE((SELECT (COUNT(CASE WHEN status = 'completed' THEN 1 END) * 100) / NULLIF(COUNT(*), 0) FROM project_milestones WHERE project_id = p.id),0) as progress
        FROM projects p
        JOIN proposals pr ON p.proposal_id = pr.id
        JOIN users u ON pr.user_id = u.id
        ORDER BY p.created_at DESC LIMIT 10
    ");
    $stmt->execute();
} else {
    $stmt = $pdo->prepare("
        SELECT p.id, p.code, p.start_date, p.status, pr.title, u.name as researcher_name,
               COALESCE((SELECT (COUNT(CASE WHEN status = 'completed' THEN 1 END) * 100) / NULLIF(COUNT(*), 0) FROM project_milestones WHERE project_id = p.id),0) as progress
        FROM projects p
        JOIN proposals pr ON p.proposal_id = pr.id
        JOIN users u ON pr.user_id = u.id
        WHERE pr.user_id = ?
        ORDER BY p.created_at DESC LIMIT 5
    ");
    $stmt->execute([$user_id]);
}

$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

function statusBadge($s) {
    return match($s) {
        'ongoing', 'approved' => '<span class="badge bg-success">กำลังดำเนินการ</span>',
        'completed'           => '<span class="badge bg-primary">เสร็จสิ้น</span>',
        'closed'              => '<span class="badge bg-secondary">ปิดแล้ว</span>',
        default               => '<span class="badge bg-light text-dark">' . htmlspecialchars($s) . '</span>',
    };
}
?>
<div class="table-responsive">
    <table class="table table-hover align-middle mb-0" id="dashboardTable">
        <thead style="background:#f8f9fa;">
            <tr>
                <th>รหัสโครงการ</th>
                <th>ชื่อโครงการ</th>
                <th>นักวิจัย</th>
                <th>สถานะ</th>
                <th>ความก้าวหน้า</th>
                <th>วันที่เริ่ม</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($projects)): foreach ($projects as $row): ?>
            <tr>
                <td><code><?php echo htmlspecialchars($row['code'] ?? '-'); ?></code></td>
                <td>
                    <a href="project_detail.php?id=<?php echo $row['id']; ?>" class="text-decoration-none fw-semibold text-dark">
                        <?php echo htmlspecialchars($row['title']); ?>
                    </a>
                </td>
                <td><?php echo htmlspecialchars($row['researcher_name'] ?? '-'); ?></td>
                <td><?php echo statusBadge($row['status']); ?></td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="height:6px;">
                            <div class="progress-bar <?php echo $row['progress'] >= 100 ? 'bg-success' : 'bg-warning'; ?>"
                                 style="width:<?php echo min($row['progress'],100); ?>%"></div>
                        </div>
                        <small class="fw-bold"><?php echo (int)$row['progress']; ?>%</small>
                    </div>
                </td>
                <td><?php echo $row['start_date'] ? date('d/m/Y', strtotime($row['start_date'])) : '-'; ?></td>
            </tr>
        <?php endforeach; else: ?>
            <tr>
                <td colspan="6" class="text-center py-4 text-muted">ยังไม่มีโครงการ</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
