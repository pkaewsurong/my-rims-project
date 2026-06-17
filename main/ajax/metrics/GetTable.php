<?php
// main/ajax/metrics/GetTable.php
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');
if (!isLoggedIn()) { http_response_code(401); exit; }

$thaiYear = (int)($_POST['year'] ?? (date('Y') + 543));
$gregorianYear = $thaiYear - 543;
$keyword = trim($_POST['keyword'] ?? '');

$whereUser = $keyword ? "AND u.name LIKE ?" : '';
$params = $keyword ? ['%' . $keyword . '%'] : [];

$sql = "
    SELECT u.name,
           (SELECT COUNT(*) FROM projects j JOIN proposals pr ON j.proposal_id = pr.id
            WHERE pr.user_id = u.id AND j.status = 'closed' AND YEAR(pr.created_at) = ?) as completed_count,
           (SELECT COALESCE(SUM(f.amount), 0) FROM proposal_funding_sources f JOIN proposals pr ON f.proposal_id = pr.id
            WHERE pr.user_id = u.id AND pr.status IN ('approved','closed') AND YEAR(pr.created_at) = ?) as total_grant,
           COALESCE(m.h_index, 0) as h_index,
           COALESCE(m.total_citations, 0) as citations,
           COALESCE(m.total_publications, 0) as publications
    FROM users u
    JOIN model_has_roles mhr ON u.id = mhr.model_id
    JOIN roles r ON mhr.role_id = r.id
    LEFT JOIN metric_snapshots m ON u.id = m.user_id AND m.fiscal_year = ?
    WHERE r.name IN ('Researcher', 'Research Admin')
    $whereUser
    ORDER BY completed_count DESC, total_grant DESC
";

$allParams = [$gregorianYear, $gregorianYear, $gregorianYear];
if ($keyword) $allParams[] = '%' . $keyword . '%';

$stmt = $pdo->prepare($sql);
$stmt->execute($allParams);
$researchers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle fs-6" id="metricsTable">
        <thead class="table-dark">
            <tr>
                <th>นักวิจัย</th>
                <th class="text-center">โครงการที่สำเร็จ</th>
                <th class="text-center">งานตีพิมพ์</th>
                <th class="text-center">H-Index</th>
                <th class="text-center">Citations</th>
                <th class="text-end">งบประมาณรวม (บาท)</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($researchers)): foreach ($researchers as $row): ?>
            <tr>
                <td class="fw-semibold"><?php echo htmlspecialchars($row['name']); ?></td>
                <td class="text-center">
                    <span class="badge bg-<?php echo $row['completed_count'] > 0 ? 'success' : 'light text-dark'; ?> fs-6">
                        <?php echo $row['completed_count']; ?>
                    </span>
                </td>
                <td class="text-center"><?php echo $row['publications']; ?></td>
                <td class="text-center">
                    <span class="fw-bold <?php echo $row['h_index'] > 0 ? 'text-primary' : 'text-muted'; ?>">
                        <?php echo $row['h_index']; ?>
                    </span>
                </td>
                <td class="text-center"><?php echo number_format((int)$row['citations']); ?></td>
                <td class="text-end"><?php echo $row['total_grant'] ? number_format((float)$row['total_grant'], 0) : '-'; ?></td>
            </tr>
        <?php endforeach; else: ?>
            <tr>
                <td colspan="6" class="text-center py-5 text-muted">
                    <i class="ri-bar-chart-box-line" style="font-size:40px; display:block; margin-bottom:8px;"></i>
                    ไม่พบข้อมูลตัวชี้วัด
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
