// main/js/proposals_form.js - จัดการฟอร์มบันทึกข้อเสนอโครงการ

function calculateStandalonePIProportion() {
    const inputs = document.querySelectorAll('#standaloneTeamContainer .team-proportion-input');
    let totalCoi = 0;
    inputs.forEach(input => {
        totalCoi += parseInt(input.value) || 0;
    });
    
    const piInput = document.getElementById('standalone_pi_proportion');
    if (piInput) {
        let newPi = 100 - totalCoi;
        if (newPi < 0) {
            Swal.fire({
                icon: 'warning',
                title: 'สัดส่วนรวมเกิน 100%',
                text: 'สัดส่วนการทำงานของทีมวิจัยรวมกันต้องไม่เกิน 100%'
            });
            newPi = 0;
        }
        piInput.value = newPi;
    }
}

function addStandaloneTeamRow() {
    const container = document.getElementById('standaloneTeamContainer');
    const row = document.createElement('div');
    row.className = 'row g-2 mb-2 team-row align-items-center';
    row.innerHTML = `
        <div class="col-md-5">
            <input type="text" name="team_name[]" class="form-control" placeholder="ชื่อ-นามสกุลผู้ร่วมวิจัย">
        </div>
        <div class="col-md-4">
            <select name="team_role[]" class="form-select">
                <option value="Co-Investigator">Co-Investigator</option>
                <option value="Research Assistant">Research Assistant</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" name="team_proportion[]" class="form-control text-center team-proportion-input" placeholder="สัดส่วน %" min="0" max="100" value="0" oninput="calculateStandalonePIProportion()">
        </div>
        <div class="col-md-1 text-center">
            <button type="button" class="btn btn-outline-danger" onclick="removeTeamRow(this)">
                <i class="ri-delete-bin-line"></i>
            </button>
        </div>
    `;
    container.appendChild(row);
    calculateStandalonePIProportion();
}

function removeTeamRow(btn) {
    btn.closest('.team-row').remove();
    calculateStandalonePIProportion();
}

// Format currency inputs on load and input
$(document).ready(function() {
    const amountInputs = document.querySelectorAll('.amount-input');
    
    amountInputs.forEach(input => {
        if(input.value) {
            let val = input.value.replace(/,/g, '');
            if(!isNaN(val) && val !== '') {
                const parts = val.split('.');
                if (parts.length > 1) {
                    input.value = Number(parts[0]).toLocaleString('en-US') + '.' + parts[1];
                } else {
                    input.value = Number(val).toLocaleString('en-US');
                }
            }
        }
        
        input.addEventListener('input', function(e) {
            let val = this.value.replace(/[^0-9.]/g, '');
            const parts = val.split('.');
            if (parts.length > 2) {
                parts.pop();
                val = parts.join('.');
            }
            
            if (val) {
                if(parts.length > 1) {
                    this.value = Number(parts[0]).toLocaleString('en-US') + '.' + parts[1];
                } else {
                    this.value = Number(val).toLocaleString('en-US');
                }
            } else {
                this.value = '';
            }
        });
    });
});

let milestoneIndex = $('#standaloneMilestonesContainer .milestone-row').length || 1;

function addStandaloneMilestoneRow() {
    const container = document.getElementById('standaloneMilestonesContainer');
    const row = document.createElement('div');
    row.className = 'row g-2 mb-2 milestone-row align-items-start';
    row.id = `milestone-row-${milestoneIndex}`;
    row.innerHTML = `
        <div class="col-md-5">
            <input type="text" name="milestone_name[]" class="form-control" placeholder="ชื่อระยะเวลา (เช่น งวดที่ 2)" required>
        </div>
        <div class="col-md-6">
            <textarea name="milestone_description[]" class="form-control" rows="1" placeholder="รายละเอียดของงานที่จะทำ" required></textarea>
        </div>
        <div class="col-md-1 text-center">
            <button type="button" class="btn btn-outline-danger" onclick="removeMilestoneRow(${milestoneIndex})">
                <i class="ri-delete-bin-line"></i>
            </button>
        </div>
    `;
    container.appendChild(row);
    milestoneIndex++;
}

function removeMilestoneRow(index) {
    const row = document.getElementById(`milestone-row-${index}`);
    if (row) row.remove();
}

function saveStandaloneProposal(status) {
    const form = document.getElementById('standaloneProposalForm');
    
    // Quick validation for submitted status
    if (status === 'submitted') {
        const titleInput = form.querySelector('input[name="title"]');
        if (!titleInput.value.trim()) {
            Swal.fire({
                icon: 'warning',
                title: 'กรุณากรอกข้อมูล',
                text: 'กรุณาระบุชื่อโครงการวิจัยก่อนส่งข้อเสนอ'
            });
            titleInput.focus();
            return;
        }
    }

    const formData = new FormData(form);
    formData.append('status', status);

    const actionUrl = 'ajax/proposals/SaveProposal.php';

    $.ajax({
        type: 'POST',
        url: actionUrl,
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(res) {
            if (res.result === 1) {
                Swal.fire({
                    icon: 'success',
                    title: status === 'draft' ? 'บันทึกแบบร่างเรียบร้อย' : 'ส่งข้อเสนอสำเร็จ',
                    text: 'ระบบกำลังนำทางคุณกลับไปหน้าโครงการ',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = 'projects.php';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'พบข้อผิดพลาด',
                    text: res.message
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'ล้มเหลว',
                text: 'เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์'
            });
        }
    });
}
