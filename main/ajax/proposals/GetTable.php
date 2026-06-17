<?php
// main/ajax/proposals/GetTable.php
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');
if (!isLoggedIn()) { http_response_code(401); exit; }

$user_id   = authUser()['id'];
$isAdmin   = hasRole('admin');
$keyword   = trim($_POST['keyword'] ?? '');
$status    = trim($_POST['status'] ?? '');
$fundingId = trim($_POST['funding_id'] ?? '');

$params = [];
$wheres = ['1=1'];

// Role filter
if (!$isAdmin) {
    $wheres[] = 'p.user_id = ?';
    $params[] = $user_id;
}
// Exclude closed proposals from proposals page
$wheres[] = '(p.status != "closed" OR p.status IS NULL)';

if ($keyword !== '') {
    $wheres[] = '(p.title LIKE ? OR u.name LIKE ?)';
    $like = '%' . $keyword . '%';
    $params[] = $like; $params[] = $like;
}
if ($status !== '') {
    $wheres[] = 'p.status = ?';
    $params[] = $status;
}
if ($fundingId !== '') {
    $wheres[] = 'p.funding_source_id = ?';
    $params[] = $fundingId;
}

$whereSQL = implode(' AND ', $wheres);

$sql = "
    SELECT p.*, f.name as funding_source_name, u.name as pi_name,
           pj.closure_requested, pj.id as project_id
    FROM proposals p
    LEFT JOIN funding_sources f ON p.funding_source_id = f.id
    LEFT JOIN users u ON p.user_id = u.id
    LEFT JOIN projects pj ON p.id = pj.proposal_id
    WHERE $whereSQL
    ORDER BY p.created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$proposals = $stmt->fetchAll(PDO::FETCH_ASSOC);

function getProposalBadge($status) {
    return match(strtolower($status ?? '')) {
        'draft'        => ['label' => 'แบบร่าง', 'color' => 'secondary'],
        'submitted'    => ['label' => 'รอพิจารณา', 'color' => 'info text-dark'],
        'under_review' => ['label' => 'อยู่ระหว่างพิจารณา', 'color' => 'warning text-dark'],
        'needs_revision' => ['label' => 'ให้กลับไปแก้ไข', 'color' => 'danger'],
        'approved'     => ['label' => 'อนุมัติแล้ว', 'color' => 'success'],
        'rejected'     => ['label' => 'ไม่อนุมัติ', 'color' => 'danger'],
        default        => ['label' => $status, 'color' => 'light text-dark'],
    };
}
?>
<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle fs-6" id="proposalsTable">
        <thead class="table-dark">
            <tr>
                <th>ชื่อข้อเสนอโครงการ</th>
                <th>นักวิจัย</th>
                <th>แหล่งทุน</th>
                <th>งบประมาณ (บาท)</th>
                <th>สถานะ</th>
                <th>วันที่ยื่น</th>
                <th class="text-center">จัดการ</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($proposals)): foreach ($proposals as $row):
            $badge = getProposalBadge($row['status']);
        ?>
            <tr>
                <td>
                    <a href="proposal_detail.php?id=<?php echo $row['id']; ?>" class="fw-semibold text-decoration-none text-dark">
                        <?php echo htmlspecialchars($row['title'] ?? '-'); ?>
                    </a>
                    <?php if ($row['project_id']): ?>
                    <br><small class="text-muted"><i class="ri-folder-line"></i> มีโครงการแล้ว</small>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($row['pi_name'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($row['funding_source_name'] ?? '-'); ?></td>
                <td class="text-end">
                    <?php echo $row['budget_total'] ? number_format((float)$row['budget_total'], 0) : '-'; ?>
                </td>
                <td>
                    <span class="badge bg-<?php echo $badge['color']; ?>">
                        <?php echo $badge['label']; ?>
                    </span>
                </td>
                <td><?php echo $row['created_at'] ? date('d/m/Y', strtotime($row['created_at'])) : '-'; ?></td>
                <td class="text-center">
                    <a href="proposal_detail.php?id=<?php echo $row['id']; ?>"
                       class="btn btn-sm btn-outline-dark me-1" title="ดูรายละเอียด">
                        <i class="ri-eye-line"></i>
                    </a>
                    <?php if (in_array($row['status'], ['submitted', 'under_review', 'needs_revision']) && hasRole('admin')): ?>
                    <button class="btn btn-sm btn-outline-success me-1"
                            onclick="reviewProposal(<?php echo $row['id']; ?>)" title="พิจารณาข้อเสนอ">
                        <i class="ri-check-double-line"></i>
                    </button>
                    <?php endif; ?>
                    <?php if ($row['status'] === 'draft' || $row['status'] === 'needs_revision'): ?>
                    <button onclick="openProposalModal(<?php echo $row['id']; ?>)"
                       class="btn btn-sm btn-outline-primary me-1" title="แก้ไข">
                        <i class="ri-edit-2-line"></i>
                    </button>
                    <?php endif; ?>
                    <?php if ($row['status'] === 'draft'): ?>
                    <button class="btn btn-sm btn-outline-danger"
                            onclick="deleteDraft(<?php echo $row['id']; ?>)" title="ลบ">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                    <?php endif; ?>
                    <?php if ($row['project_id'] && hasRole('admin') && !$row['closure_requested']): ?>
                    <button class="btn btn-sm btn-outline-warning"
                            onclick="approveClosure(<?php echo $row['project_id']; ?>)" title="อนุมัติปิดโครงการ">
                        <i class="ri-close-circle-line"></i>
                    </button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; else: ?>
            <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                    <i class="ri-file-list-3-line" style="font-size:40px; display:block; margin-bottom:8px;"></i>
                    ไม่พบข้อเสนอโครงการ
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
