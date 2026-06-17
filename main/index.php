<?php
// main/index.php - Research Performance Dashboard
$pageTitle = 'ภาพรวม (Dashboard)';
$pageCss   = 'dashboard';
$pageJs    = 'dashboard';
require 'header.php';

// Fetch all researchers for filter dropdown
$stmtAllResearchers = $pdo->query("
    SELECT u.id, u.name
    FROM users u
    JOIN model_has_roles mhr ON u.id = mhr.model_id
    JOIN roles r ON mhr.role_id = r.id
    WHERE r.name IN ('Researcher', 'Research Admin')
    ORDER BY u.name ASC
");
$allResearchers = $stmtAllResearchers->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h1 class="page-title"><i class="ri-dashboard-line me-2"></i>แดชบอร์ดสมรรถนะวิจัย (Research Dashboard)</h1>
        <p class="text-muted mb-0" style="font-size:14px;">ระบบบริหารงานวิจัยและนวัตกรรม (RIMS) | ภาพรวมการดำเนินงานวิจัยและตัวชี้วัด</p>
    </div>
    
    <!-- Filter Controls -->
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <!-- Year Selection -->
        <select id="filterYear" class="form-select" style="width: auto;">
            <?php for($y = date('Y')+543; $y >= date('Y')+539; $y--): ?>
                <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
            <?php endfor; ?>
        </select>

        <!-- Researcher Selection -->
        <select id="filterResearcher" class="form-select select2-filter" style="min-width: 220px;">
            <option value="">ทั้งหมด (ไม่ระบุนักวิจัย)</option>
            <?php foreach ($allResearchers as $r): ?>
                <option value="<?php echo $r['id']; ?>"><?php echo htmlspecialchars($r['name']); ?></option>
            <?php endforeach; ?>
        </select>

        <button onclick="LoadDashboardData()" class="btn btn-dark fw-bold">
            <i class="ri-search-line"></i> ค้นหา
        </button>
    </div>
</div>

<!-- KPI Cards Row -->
<div class="row g-4 mb-4" id="statsRow">
    <!-- Will be filled by AJAX -->
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-4 border-bottom pb-2">เทรนด์ผลงานวิจัย 5 ปีล่าสุด</h5>
                <div style="height: 300px; position: relative;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-4 border-bottom pb-2">สัดส่วนแหล่งทุน (ภายใน/ภายนอก)</h5>
                <div style="height: 300px; position: relative;" class="d-flex justify-content-center">
                    <canvas id="fundingChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Status Distribution -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-4 border-bottom pb-2">สถานะข้อเสนอโครงการ</h5>
                <div class="d-flex flex-column gap-3" id="statusList">
                    <!-- Will be filled by AJAX -->
                </div>
            </div>
        </div>
    </div>

    <!-- Top Researchers -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <h5 class="fw-bold mb-0">นักวิจัยที่มีผลงานโดดเด่น (Top 5)</h5>
                    <a href="metrics.php" class="btn btn-sm btn-outline-dark">ดูทั้งหมด</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ชื่อ-สกุล</th>
                                <th class="text-center">โครงการที่สมบูรณ์</th>
                                <th class="text-center">ทุนวิจัยรวม</th>
                                <th class="text-center">H-Index</th>
                            </tr>
                        </thead>
                        <tbody id="topResearchersBody">
                            <!-- Will be filled by AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require 'footer.php'; ?>