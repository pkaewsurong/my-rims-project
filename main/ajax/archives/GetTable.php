<?php
// main/ajax/archives/GetTable.php
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');
if (!isLoggedIn()) { http_response_code(401); exit; }

$user_id   = authUser()['id'];
$keyword   = trim($_POST['keyword'] ?? '');
$projectId = trim($_POST['project_id'] ?? '');
$access    = trim($_POST['access'] ?? '');

$wheres  = ['pr.user_id = ?'];
$params  = [$user_id];

if ($keyword !== '') {
    $wheres[] = 'da.file_name LIKE ?';
    $params[] = '%' . $keyword . '%';
}
if ($projectId !== '') {
    $wheres[] = 'p.id = ?';
    $params[] = $projectId;
}
if ($access !== '') {
    $dbLevel = match($access) {
        'public'     => 'Public Domain',
        'restricted' => 'Restricted',
        'private'    => 'Highly Confidential',
        default      => $access
    };
    $wheres[] = 'da.privacy_level = ?';
    $params[] = $dbLevel;
}

$whereSQL = implode(' AND ', $wheres);

$sql = "
    SELECT da.*, pr.title as project_title, p.code as project_code
    FROM data_archives da
    JOIN projects p ON da.project_id = p.id
    JOIN proposals pr ON p.proposal_id = pr.id
    WHERE $whereSQL
    ORDER BY da.created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$archives = $stmt->fetchAll(PDO::FETCH_ASSOC);

function accessBadge($level) {
    return match($level) {
        'Public Domain'       => '<span class="badge bg-success">สาธารณะ</span>',
        'Restricted'          => '<span class="badge bg-warning text-dark">จำกัด</span>',
        'Highly Confidential' => '<span class="badge bg-secondary">ส่วนตัว</span>',
        default               => '<span class="badge bg-light text-dark">' . htmlspecialchars($level) . '</span>',
    };
}

function formatSize($bytes) {
    if ($bytes >= 1048576) return round($bytes/1048576, 2) . ' MB';
    if ($bytes >= 1024) return round($bytes/1024, 1) . ' KB';
    return $bytes . ' B';
}
?>
<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle fs-6" id="archivesTable">
        <thead class="table-dark">
            <tr>
                <th>ชื่อชุดข้อมูล</th>
                <th>โครงการ</th>
                <th>ประเภทข้อมูล</th>
                <th>ระดับการเข้าถึง</th>
                <th>ขนาดไฟล์</th>
                <th>วันที่อัปโหลด</th>
                <th class="text-center">จัดการ</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($archives)): foreach ($archives as $row): ?>
            <tr>
                <td class="fw-semibold"><?php echo htmlspecialchars($row['file_name']); ?></td>
                <td>
                    <small><code><?php echo htmlspecialchars($row['project_code'] ?? '-'); ?></code></small>
                    <div style="font-size:12px; color:#888;"><?php echo htmlspecialchars($row['project_title'] ?? '-'); ?></div>
                </td>
                <td><?php echo htmlspecialchars($row['category'] ?? '-'); ?></td>
                <td><?php echo accessBadge($row['privacy_level']); ?></td>
                <td><?php echo $row['file_size_kb'] ? formatSize((int)$row['file_size_kb'] * 1024) : '-'; ?></td>
                <td><?php echo $row['created_at'] ? date('d/m/Y', strtotime($row['created_at'])) : '-'; ?></td>
                <td class="text-center">
                    <?php if ($row['file_path']): ?>
                    <a href="../public/uploads/archives/<?php echo htmlspecialchars($row['file_path']); ?>"
                       class="btn btn-sm btn-outline-dark" target="_blank" title="ดาวน์โหลด">
                        <i class="ri-download-line"></i>
                    </a>
                    <?php else: ?>
                    <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; else: ?>
            <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                    <i class="ri-archive-line" style="font-size:40px; display:block; margin-bottom:8px;"></i>
                    ไม่พบข้อมูลในคลังข้อมูลวิจัย
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
