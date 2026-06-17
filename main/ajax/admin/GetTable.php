<?php
// main/ajax/admin/GetTable.php
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');
if (!isLoggedIn() || (!hasRole('admin') && !hasRole('research_admin'))) { http_response_code(403); exit; }

$tab     = trim($_POST['tab'] ?? 'funders');
$keyword = trim($_POST['keyword'] ?? '');

if ($tab === 'funders') {
    $sql    = "SELECT id, name, type, status FROM funders WHERE 1=1";
    $params = [];
    if ($keyword) { $sql .= " AND name LIKE ?"; $params[] = '%'.$keyword.'%'; }
    $sql .= " ORDER BY name";
    $stmt = $pdo->prepare($sql); $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle fs-6" id="adminTable">
            <thead class="table-dark">
                <tr><th>ชื่อแหล่งทุน</th><th>ประเภท</th><th>สถานะ</th></tr>
            </thead>
            <tbody>
            <?php if (!empty($rows)): foreach ($rows as $r): ?>
                <tr>
                    <td class="fw-semibold"><?php echo htmlspecialchars($r['name']); ?></td>
                    <td><?php echo htmlspecialchars($r['type'] ?? '-'); ?></td>
                    <td><?php echo $r['status'] === 'active' ? '<span class="badge bg-success">ใช้งาน</span>' : '<span class="badge bg-secondary">ระงับ</span>'; ?></td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="3" class="text-center text-muted py-4">ไม่พบข้อมูลแหล่งทุน</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php

} elseif ($tab === 'journals') {
    $sql    = "SELECT id, name, issn, quartile, database_index FROM journals WHERE 1=1";
    $params = [];
    if ($keyword) { $sql .= " AND name LIKE ?"; $params[] = '%'.$keyword.'%'; }
    $sql .= " ORDER BY name";
    $stmt = $pdo->prepare($sql); $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle fs-6" id="adminTable">
            <thead class="table-dark">
                <tr><th>ชื่อวารสาร</th><th>ISSN</th><th>Quartile</th><th>ฐานข้อมูล</th></tr>
            </thead>
            <tbody>
            <?php if (!empty($rows)): foreach ($rows as $r): ?>
                <tr>
                    <td class="fw-semibold"><?php echo htmlspecialchars($r['name']); ?></td>
                    <td><code><?php echo htmlspecialchars($r['issn'] ?? '-'); ?></code></td>
                    <td><?php echo $r['quartile'] ? '<span class="badge bg-primary">'.htmlspecialchars($r['quartile']).'</span>' : '-'; ?></td>
                    <td><?php echo htmlspecialchars($r['database_index'] ?? '-'); ?></td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="4" class="text-center text-muted py-4">ไม่พบข้อมูลวารสาร</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php

} elseif ($tab === 'tiers') {
    $sql    = "SELECT id, category, level_name, description, points FROM metric_tiers WHERE 1=1";
    $params = [];
    if ($keyword) { $sql .= " AND (category LIKE ? OR level_name LIKE ?)"; $params[] = '%'.$keyword.'%'; $params[] = '%'.$keyword.'%'; }
    $sql .= " ORDER BY category ASC, points DESC";
    $stmt = $pdo->prepare($sql); $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle fs-6" id="adminTable">
            <thead class="table-dark">
                <tr><th>หมวดหมู่</th><th>ระดับ</th><th>คะแนน</th><th>คำอธิบาย</th></tr>
            </thead>
            <tbody>
            <?php if (!empty($rows)): foreach ($rows as $r): ?>
                <tr>
                    <td class="fw-semibold"><?php echo htmlspecialchars($r['category']); ?></td>
                    <td><?php echo htmlspecialchars($r['level_name']); ?></td>
                    <td><span class="badge bg-warning text-dark fw-bold fs-6"><?php echo $r['points']; ?></span></td>
                    <td class="text-muted" style="font-size:13px;"><?php echo htmlspecialchars($r['description'] ?? '-'); ?></td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="4" class="text-center text-muted py-4">ไม่พบข้อมูลเกณฑ์</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
