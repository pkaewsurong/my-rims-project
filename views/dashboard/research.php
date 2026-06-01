<?php ob_start(); ?>
<!-- views/dashboard/research.php -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="mb-8">
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center bg-white border border-slate-200 rounded-2xl p-6 shadow-sm gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-wide flex items-center">
                <svg class="w-6 h-6 mr-3 text-indigo-650" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                แดชบอร์ดสมรรถนะวิจัย (Research Dashboard)
            </h2>
            <p class="mt-1 text-sm text-slate-500 font-medium">ระบบบริหารงานวิจัยและนวัตกรรม (RIMS) | ภาพรวมการดำเนินงานวิจัยและตัวชี้วัด</p>
        </div>
        <div>
            <form id="dashboardFilterForm" action="/public/dashboard" method="GET" class="flex flex-wrap items-center gap-2">
                <!-- Year Dropdown -->
                <select name="year" class="px-4 py-2.5 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                    <?php for($y = date('Y')+543; $y >= date('Y')+539; $y--): ?>
                        <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>

                <!-- Researcher Searchable Dropdown -->
                <div class="relative" id="researcherDropdownContainer">
                    <input type="hidden" name="researcher_id" id="researcherIdInput" value="<?= $researcherId ?? '' ?>">
                    <input 
                        type="text" 
                        id="researcherSearchInput" 
                        placeholder="🔍 ค้นหานักวิจัย..." 
                        autocomplete="off"
                        value="<?php 
                            if ($researcherId) {
                                foreach ($allResearchers as $r) {
                                    if ($r['id'] == $researcherId) { echo e($r['name']); break; }
                                }
                            }
                        ?>"
                        class="w-56 px-4 py-2.5 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm"
                    >
                    <div id="researcherDropdownList" class="hidden absolute z-50 mt-2 w-72 max-h-60 overflow-y-auto bg-white border border-slate-200 rounded-xl shadow-xl">
                        <div class="p-1">
                            <div 
                                class="researcher-option px-3 py-2 text-sm text-slate-500 hover:bg-slate-100 rounded-lg cursor-pointer transition-colors font-medium"
                                data-id="" 
                                data-name=""
                            >
                                ทั้งหมด (ไม่ระบุนักวิจัย)
                            </div>
                            <?php foreach ($allResearchers as $researcher): ?>
                            <div 
                                class="researcher-option px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-lg cursor-pointer transition-colors font-semibold <?= $researcherId == $researcher['id'] ? 'bg-indigo-50 text-indigo-650' : '' ?>"
                                data-id="<?= $researcher['id'] ?>" 
                                data-name="<?= e($researcher['name']) ?>"
                            >
                                <?= e($researcher['name']) ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <button type="submit" class="px-5 py-2.5 bg-indigo-50 border border-indigo-200 text-indigo-650 hover:bg-indigo-100 rounded-xl text-sm font-semibold transition-colors shadow-sm">
                    ดูข้อมูล
                </button>
                <?php if ($researcherId): ?>
                <a href="/public/dashboard?year=<?= $year ?>" class="px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-650 bg-white hover:bg-slate-50 transition-colors shadow-sm">
                    ดูทั้งหมด
                </a>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <?php if ($researcherId): ?>
    <div class="mt-3 px-4 py-3 bg-indigo-50/50 border border-indigo-100 rounded-xl flex items-center gap-2">
        <svg class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path></svg>
        <span class="text-sm text-indigo-850 font-medium">กำลังแสดงข้อมูลของ: <strong class="text-slate-800 font-bold"><?php 
            foreach ($allResearchers as $r) { 
                if ($r['id'] == $researcherId) { echo e($r['name']); break; } 
            } 
        ?></strong></span>
    </div>
    <?php endif; ?>
</div>

<div class="max-w-7xl mx-auto pb-24 space-y-6">

    <!-- KPI Cards Setup -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card p-5 relative overflow-hidden group">
            <p class="text-sm font-semibold text-slate-500 mb-1">นักวิจัยรวม</p>
            <h3 class="text-3xl font-extrabold text-slate-800 flex items-end gap-2">
                <?= number_format($snapshot['total_researchers'] ?? 0) ?> <span class="text-xs text-slate-450 font-bold pb-1">คน</span>
            </h3>
        </div>

        <div class="card p-5 relative overflow-hidden group border-indigo-200">
            <p class="text-sm font-semibold text-slate-500 mb-1">โครงการที่สมบูรณ์แล้ว</p>
            <h3 class="text-3xl font-extrabold text-slate-800 flex items-end gap-2">
                <?= number_format($snapshot['total_completed']) ?> <span class="text-xs text-indigo-600 font-bold pb-1">(High Impact = <?= number_format($snapshot['high_impact_completed']) ?>)</span>
            </h3>
        </div>

        <div class="card p-5 relative overflow-hidden group border-amber-200">
            <p class="text-sm font-semibold text-slate-500 mb-1">ทุนวิจัยภายนอก</p>
            <h3 class="text-3xl font-extrabold text-slate-800 flex items-end gap-2">
                <?= number_format($snapshot['external_grants_value'] / 1000000, 2) ?>M <span class="text-xs text-slate-450 font-bold pb-1">บาท</span>
            </h3>
        </div>
    </div>

    <!-- Charts Area -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-6 border-b border-slate-100 pb-3">เทรนด์ผลงานวิจัย 5 ปีล่าสุด</h3>
            <div class="h-64 object-contain">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
        <div class="card p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-6 border-b border-slate-100 pb-3">สัดส่วนแหล่งทุน (ภายใน/ภายนอก)</h3>
            <div class="h-64 flex justify-center">
                <canvas id="fundingChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Status -->
        <div class="card p-6 lg:col-span-1">
            <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-3">สถานะข้อเสนอโครงการ</h3>
            <div class="space-y-4 mt-6">
                <?php 
                    $statConfig = [
                        'closed' => ['color' => 'bg-rose-500', 'label' => 'ปิดโครงการแล้ว'],
                        'approved' => ['color' => 'bg-emerald-500', 'label' => 'อนุมัติแล้ว'],
                        'under_review' => ['color' => 'bg-indigo-500', 'label' => 'อยู่ระหว่างพิจารณา'],
                        'submitted' => ['color' => 'bg-amber-500', 'label' => 'รอตรวจสอบ'],
                        'draft' => ['color' => 'bg-slate-400', 'label' => 'ฉบับร่าง']
                    ];
                    $totalProj = array_sum($projectStatusData);
                    foreach($statConfig as $key => $conf): 
                        $val = $projectStatusData[$key] ?? 0;
                        $pct = $totalProj > 0 ? round(($val/$totalProj)*100) : 0;
                ?>
                <div>
                    <div class="flex justify-between text-xs mb-1.5 text-slate-500 font-semibold">
                        <span><?= $conf['label'] ?></span>
                        <span class="font-bold text-slate-800"><?= $val ?> (<?= $pct ?>%)</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="<?= $conf['color'] ?> h-2 rounded-full" style="width: <?= $pct ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Top Researchers -->
        <div class="card p-6 lg:col-span-2">
            <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-3">
                <h3 class="text-lg font-bold text-slate-800">นักวิจัยที่มีผลงานโดดเด่น (Top 5)</h3>
                <a href="/public/dashboard/researchers?year=<?= $year ?>" class="text-xs text-indigo-600 hover:text-indigo-850 transition-colors font-bold">ดูทั้งหมด →</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-xs uppercase tracking-wider text-slate-500 bg-slate-50">
                            <th class="px-4 py-3 rounded-tl-lg font-semibold">ชื่อ-สกุล</th>
                            <th class="px-4 py-3 font-semibold text-center">โครงการที่สมบูรณ์</th>
                            <th class="px-4 py-3 font-semibold text-center">ทุนวิจัย</th>
                            <th class="px-4 py-3 font-semibold text-center rounded-tr-lg">H-Index</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach($topResearchers as $i => $tr): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 py-3.5 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 border <?=$i < 3 ? 'border-amber-300 text-amber-700 bg-amber-50':'border-slate-200 text-slate-550'?> flex items-center justify-center font-bold text-xs shadow-sm">#<?= $i+1 ?></div>
                                <span class="text-sm font-bold text-slate-800"><?= e($tr['name']) ?></span>
                            </td>
                            <td class="px-4 py-3.5 text-center text-sm font-bold text-emerald-600"><?= number_format($tr['completed_count'] ?? 0) ?></td>
                            <td class="px-4 py-3.5 text-center text-sm font-bold text-amber-600"><?= number_format($tr['total_grant'] ?? 0) ?></td>
                            <td class="px-4 py-3.5 text-center text-sm font-mono font-bold text-slate-700"><?= number_format($tr['h_index'] ?? 0) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        Chart.defaults.color = '#64748b';
        Chart.defaults.borderColor = '#f1f5f9';

        const trendData = <?php echo json_encode($trends); ?>;
        const labels = trendData.map(d => d.year);
        const completed = trendData.map(d => d.completed);
        const grants = trendData.map(d => d.grants);

        // Trend Chart
        const ctxTrend = document.getElementById('trendChart').getContext('2d');
        new Chart(ctxTrend, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'โครงการที่สมบูรณ์ (โครงการ)',
                        data: completed,
                        backgroundColor: 'rgba(79, 70, 229, 0.7)',
                        borderColor: '#4F46E5',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'เงินทุน (ล้านบาท)',
                        data: grants,
                        type: 'line',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        borderColor: '#F59E0B',
                        borderWidth: 2,
                        tension: 0.3,
                        pointBackgroundColor: '#F59E0B',
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: { type: 'linear', display: true, position: 'left' },
                    y1: { type: 'linear', display: true, position: 'right', grid: { drawOnChartArea: false } }
                },
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Funding Chart
        const fundingProportion = <?php echo json_encode(array_values($fundingProportion)); ?>;
        const ctxFund = document.getElementById('fundingChart').getContext('2d');
        new Chart(ctxFund, {
            type: 'doughnut',
            data: {
                labels: ['ทุนภายใน (Internal)', 'ทุนภายนอก (External)'],
                datasets: [{
                    data: fundingProportion,
                    backgroundColor: [
                        'rgba(99, 102, 241, 0.7)',
                        'rgba(168, 85, 247, 0.7)'
                    ],
                    borderColor: [
                        '#6366f1',
                        '#a855f7'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    });

    // Researcher Searchable Dropdown Logic
    (function() {
        const searchInput = document.getElementById('researcherSearchInput');
        const dropdownList = document.getElementById('researcherDropdownList');
        const hiddenInput = document.getElementById('researcherIdInput');
        const container = document.getElementById('researcherDropdownContainer');
        const options = dropdownList.querySelectorAll('.researcher-option');

        searchInput.addEventListener('focus', function() {
            dropdownList.classList.remove('hidden');
            filterOptions(this.value);
        });

        searchInput.addEventListener('input', function() {
            dropdownList.classList.remove('hidden');
            hiddenInput.value = ''; 
            filterOptions(this.value);
        });

        options.forEach(function(option) {
            option.addEventListener('mousedown', function(e) {
                e.preventDefault(); 
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                hiddenInput.value = id;
                searchInput.value = name;
                dropdownList.classList.add('hidden');
            });
        });

        document.addEventListener('click', function(e) {
            if (!container.contains(e.target)) {
                dropdownList.classList.add('hidden');
            }
        });

        function filterOptions(query) {
            const q = query.toLowerCase().trim();
            options.forEach(function(option) {
                const name = (option.getAttribute('data-name') || option.textContent).toLowerCase();
                if (q === '' || name.includes(q)) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });
        }
    })();
</script>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php'; 
?>
