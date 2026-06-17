// main/js/dashboard.js - Research Performance Dashboard Controller (PCT)
let trendChartInstance = null;
let fundingChartInstance = null;

$(document).ready(function () {
    // Initialize Select2 filter
    $('.select2-filter').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    LoadDashboardData();
});

function LoadDashboardData() {
    const year = $('#filterYear').val();
    const researcherId = $('#filterResearcher').val();

    // Show loading
    $('#statsRow').html('<div class="col-12 text-center py-5"><div class="spinner-border text-dark"></div></div>');
    $('#statusList').html('<div class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div> กำลังโหลด...</div>');
    $('#topResearchersBody').html('<tr><td colspan="4" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div> กำลังโหลด...</td></tr>');

    $.ajax({
        type: 'POST',
        url: 'ajax/dashboard/GetData.php',
        data: {
            year: year,
            researcher_id: researcherId
        },
        dataType: 'json',
        success: function (res) {
            // 1. Populate stats cards
            const totalResearchers = res.snapshot.total_researchers;
            const totalCompleted = res.snapshot.total_completed;
            const highImpactCompleted = res.snapshot.high_impact_completed;
            const externalGrants = res.snapshot.external_grants_value;

            let statsHtml = `
                <div class="col-xl-4 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon green"><i class="ri-user-line"></i></div>
                        <div>
                            <div class="stat-value">${totalResearchers.toLocaleString()}</div>
                            <div class="stat-label">นักวิจัยรวม</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon blue"><i class="ri-folder-check-line"></i></div>
                        <div>
                            <div class="stat-value">${totalCompleted.toLocaleString()}</div>
                            <div class="stat-label">โครงการที่สมบูรณ์ <small class="text-muted">(High Impact: ${highImpactCompleted})</small></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon yellow"><i class="ri-money-dollar-circle-line"></i></div>
                        <div>
                            <div class="stat-value">${formatBudget(externalGrants)}</div>
                            <div class="stat-label">ทุนวิจัยภายนอก (บาท)</div>
                        </div>
                    </div>
                </div>
            `;
            $('#statsRow').html(statsHtml);

            // 2. Populate Status list
            const statConfig = {
                'closed': { 'color': 'bg-secondary', 'label': 'ปิดโครงการแล้ว' },
                'approved': { 'color': 'bg-success', 'label': 'อนุมัติแล้ว' },
                'under_review': { 'color': 'bg-warning text-dark', 'label': 'อยู่ระหว่างพิจารณา' },
                'submitted': { 'color': 'bg-info text-dark', 'label': 'รอตรวจสอบ' },
                'draft': { 'color': 'bg-dark', 'label': 'ฉบับร่าง' }
            };

            let totalProj = 0;
            Object.values(res.projectStatusData).forEach(v => { totalProj += v; });

            let statusHtml = '';
            Object.keys(statConfig).forEach(key => {
                const conf = statConfig[key];
                const val = res.projectStatusData[key] || 0;
                const pct = totalProj > 0 ? Math.round((val / totalProj) * 100) : 0;
                statusHtml += `
                    <div>
                        <div class="d-flex justify-content-between text-xs mb-1 font-semibold" style="font-size: 13px;">
                            <span>${conf.label}</span>
                            <span class="fw-bold">${val} (${pct}%)</span>
                        </div>
                        <div class="progress" style="height: 8px; border-radius: 4px;">
                            <div class="progress-bar ${conf.color}" role="progressbar" style="width: ${pct}%"></div>
                        </div>
                    </div>
                `;
            });
            if (statusHtml === '') statusHtml = '<div class="text-center py-4 text-muted">ไม่มีข้อมูลข้อเสนอโครงการ</div>';
            $('#statusList').html(statusHtml);

            // 3. Populate Top Researchers Table
            let topHtml = '';
            if (res.topResearchers && res.topResearchers.length > 0) {
                res.topResearchers.forEach((tr, i) => {
                    topHtml += `
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-dark rounded-circle" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;">#${i + 1}</span>
                                    <span class="fw-semibold">${tr.name}</span>
                                </div>
                            </td>
                            <td class="text-center text-success fw-bold">${tr.completed_count.toLocaleString()}</td>
                            <td class="text-center text-warning fw-bold">${tr.total_grant.toLocaleString()} ฿</td>
                            <td class="text-center fw-bold">${tr.h_index}</td>
                        </tr>
                    `;
                });
            } else {
                topHtml = '<tr><td colspan="4" class="text-center py-4 text-muted">ไม่พบข้อมูลนักวิจัยที่มีผลงาน</td></tr>';
            }
            $('#topResearchersBody').html(topHtml);

            // 4. Render Trend Chart
            const labels = res.trends.map(d => d.year);
            const completed = res.trends.map(d => d.completed);
            const grants = res.trends.map(d => d.grants);

            if (trendChartInstance) trendChartInstance.destroy();
            const ctxTrend = document.getElementById('trendChart').getContext('2d');
            trendChartInstance = new Chart(ctxTrend, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'โครงการที่สมบูรณ์ (โครงการ)',
                            data: completed,
                            backgroundColor: 'rgba(25, 26, 35, 0.7)',
                            borderColor: '#191a23',
                            borderWidth: 1,
                            yAxisID: 'y'
                        },
                        {
                            label: 'เงินทุน (ล้านบาท)',
                            data: grants,
                            type: 'line',
                            backgroundColor: 'rgba(185, 255, 102, 0.1)',
                            borderColor: '#84cc16',
                            borderWidth: 2,
                            tension: 0.3,
                            pointBackgroundColor: '#84cc16',
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

            // 5. Render Funding Chart
            const internalVal = res.fundingProportion.Internal;
            const externalVal = res.fundingProportion.External;

            if (fundingChartInstance) fundingChartInstance.destroy();
            const ctxFund = document.getElementById('fundingChart').getContext('2d');
            fundingChartInstance = new Chart(ctxFund, {
                type: 'doughnut',
                data: {
                    labels: ['ทุนภายใน (Internal)', 'ทุนภายนอก (External)'],
                    datasets: [{
                        data: [internalVal, externalVal],
                        backgroundColor: [
                            'rgba(25, 26, 35, 0.7)',
                            'rgba(185, 255, 102, 0.7)'
                        ],
                        borderColor: [
                            '#191a23',
                            '#b9ff66'
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
        },
        error: function () {
            $('#statsRow').html('<div class="col-12 text-center text-danger py-4">เกิดข้อผิดพลาดในการดึงข้อมูล</div>');
        }
    });
}

function formatBudget(amount) {
    amount = parseFloat(amount) || 0;
    if (amount >= 1000000) return (amount / 1000000).toFixed(2) + ' M';
    if (amount >= 1000) return (amount / 1000).toFixed(1) + ' K';
    return amount.toLocaleString();
}
