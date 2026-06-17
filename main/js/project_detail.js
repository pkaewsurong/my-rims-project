// main/js/project_detail.js - จัดการ AJAX บนหน้ารายละเอียดโครงการ

$(document).ready(function () {
    // Handle Project File Upload Form
    $('#fileUploadForm').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: 'ajax/projects/UploadProjectFile.php',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (res) {
                if (res.result === 1) {
                    Swal.fire({
                        icon: 'success',
                        title: 'อัปโหลดไฟล์สำเร็จ',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    location.reload();
                } else {
                    Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message });
                }
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ' });
            }
        });
    });
});

// Milestone actions
async function addMilestone(projectId) {
    const { value: formValues } = await Swal.fire({
        title: 'เพิ่มแผนงานย่อย (Milestone)',
        html:
            '<input id="swal-ms-name" class="swal2-input" placeholder="ชื่อแผนงานย่อย (เช่น งวดที่ 1 / เดือน 1)">' +
            '<textarea id="swal-ms-desc" class="swal2-textarea" placeholder="รายละเอียด/เป้าหมาย"></textarea>',
        focusConfirm: false,
        confirmButtonText: 'บันทึก',
        cancelButtonText: 'ยกเลิก',
        showCancelButton: true,
        confirmButtonColor: '#191a23',
        preConfirm: () => {
            return {
                name: document.getElementById('swal-ms-name').value,
                desc: document.getElementById('swal-ms-desc').value
            }
        }
    });

    if (formValues) {
        if (!formValues.name) {
            Swal.fire('คำเตือน', 'กรุณากรอกชื่อแผนงานย่อย', 'warning');
            return;
        }
        $.ajax({
            type: 'POST',
            url: 'ajax/projects/SaveMilestone.php',
            data: {
                project_id: projectId,
                name: formValues.name,
                desc: formValues.desc
            },
            dataType: 'json',
            success: function (res) {
                if (res.result === 1) {
                    location.reload();
                } else {
                    Swal.fire('ผิดพลาด', res.message, 'error');
                }
            }
        });
    }
}

function updateMilestoneStatus(id, status) {
    $.ajax({
        type: 'POST',
        url: 'ajax/projects/UpdateMilestoneStatus.php',
        data: { id: id, status: status },
        dataType: 'json',
        success: function (res) {
            if (res.result === 1) {
                showAlert('อัปเดตสถานะสำเร็จ');
            } else {
                Swal.fire('ผิดพลาด', res.message, 'error');
            }
        }
    });
}

async function deleteMilestone(id) {
    const confirmed = await showConfirm('ลบแผนงานย่อย', 'คุณแน่ใจหรือไม่ที่จะลบแผนงานย่อยนี้?', true);
    if (confirmed) {
        $.ajax({
            type: 'POST',
            url: 'ajax/projects/DeleteMilestone.php',
            data: { id: id },
            dataType: 'json',
            success: function (res) {
                if (res.result === 1) {
                    location.reload();
                } else {
                    Swal.fire('ผิดพลาด', res.message, 'error');
                }
            }
        });
    }
}

// Progress Reports Modals
function openProgressModal(projectId) {
    $('#showModal').html('<div class="d-flex justify-content-center py-5"><div class="spinner-border text-dark"></div></div>');
    $('#mainModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'ajax/projects/ProgressModal.php?project_id=' + projectId,
        dataType: 'html',
        success: function (r) {
            $('#showModal').html(r);
        }
    });
}

function viewProgressDetail(id) {
    $('#showModal').html('<div class="d-flex justify-content-center py-5"><div class="spinner-border text-dark"></div></div>');
    $('#mainModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'ajax/projects/ProgressDetailModal.php?id=' + id,
        dataType: 'html',
        success: function (r) {
            $('#showModal').html(r);
        }
    });
}

async function deleteProgressReport(id) {
    const confirmed = await showConfirm('ลบรายงานความก้าวหน้า', 'ข้อมูลรายงานรวมถึงไฟล์แนบจะถูกลบอย่างถาวร', true);
    if (confirmed) {
        $.ajax({
            type: 'POST',
            url: 'ajax/projects/DeleteProgress.php',
            data: { id: id },
            dataType: 'json',
            success: function (res) {
                if (res.result === 1) {
                    location.reload();
                } else {
                    Swal.fire('ผิดพลาด', res.message, 'error');
                }
            }
        });
    }
}

// Publications & IP Modals
function openOutputModal(projectId) {
    $('#showModal').html('<div class="d-flex justify-content-center py-5"><div class="spinner-border text-dark"></div></div>');
    $('#mainModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'ajax/projects/OutputModal.php?project_id=' + projectId,
        dataType: 'html',
        success: function (r) {
            $('#showModal').html(r);
        }
    });
}

function openIPModal(projectId) {
    $('#showModal').html('<div class="d-flex justify-content-center py-5"><div class="spinner-border text-dark"></div></div>');
    $('#mainModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'ajax/projects/IPModal.php?project_id=' + projectId,
        dataType: 'html',
        success: function (r) {
            $('#showModal').html(r);
        }
    });
}

async function deletePublication(id) {
    const confirmed = await showConfirm('ลบผลงานตีพิมพ์', 'คุณยืนยันที่จะลบข้อมูลผลงานตีพิมพ์นี้?', true);
    if (confirmed) {
        $.ajax({
            type: 'POST',
            url: 'ajax/projects/DeletePublication.php',
            data: { id: id },
            dataType: 'json',
            success: function (res) {
                if (res.result === 1) {
                    location.reload();
                } else {
                    Swal.fire('ผิดพลาด', res.message, 'error');
                }
            }
        });
    }
}

async function deleteIP(id) {
    const confirmed = await showConfirm('ลบข้อมูลทรัพย์สินทางปัญญา', 'คุณยืนยันที่จะลบข้อมูล IP นี้?', true);
    if (confirmed) {
        $.ajax({
            type: 'POST',
            url: 'ajax/projects/DeleteIP.php',
            data: { id: id },
            dataType: 'json',
            success: function (res) {
                if (res.result === 1) {
                    location.reload();
                } else {
                    Swal.fire('ผิดพลาด', res.message, 'error');
                }
            }
        });
    }
}

// Project Files
async function deleteProjectFile(id) {
    const confirmed = await showConfirm('ลบไฟล์เอกสาร', 'ไฟล์นี้จะถูกลบอย่างถาวรจากระบบ', true);
    if (confirmed) {
        $.ajax({
            type: 'POST',
            url: 'ajax/projects/DeleteProjectFile.php',
            data: { id: id },
            dataType: 'json',
            success: function (res) {
                if (res.result === 1) {
                    location.reload();
                } else {
                    Swal.fire('ผิดพลาด', res.message, 'error');
                }
            }
        });
    }
}

// Final Report Modals
function openFinalReportModal(projectId) {
    $('#showModal').html('<div class="d-flex justify-content-center py-5"><div class="spinner-border text-dark"></div></div>');
    $('#mainModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'ajax/projects/FinalReportModal.php?project_id=' + projectId,
        dataType: 'html',
        success: function (r) {
            $('#showModal').html(r);
        }
    });
}

// Request Project Closure
async function requestProjectClosure(id, progress, hasFinalReport) {
    if (progress < 100) {
        Swal.fire('ความก้าวหน้าไม่ครบ', 'ต้องรายงานความก้าวหน้าโครงการให้ครบ 100% ก่อนปิดโครงการ', 'warning');
        return;
    }
    if (!hasFinalReport) {
        Swal.fire('ขาดรายงานสรุป', 'กรุณาส่งรายงานวิจัยฉบับสมบูรณ์ (Final Report) ก่อนเสนอขอปิดโครงการ', 'warning');
        return;
    }

    const confirmed = await showConfirm('ยืนยันเสนอขอปิดโครงการ', 'ระบบจะส่งคำร้องขอปิดโครงการให้ผู้ดูแลระบบเพื่อทำการพิจารณาอนุมัติ', false);
    if (confirmed) {
        window.location.href = 'ajax/projects/RequestClosure.php?id=' + id;
    }
}

// Admin approve closure
async function approveClosure(id) {
    const confirmed = await showConfirm('อนุมัติปิดโครงการ', 'คุณแน่ใจที่จะอนุมัติการปิดโครงการวิจัยนี้หรือไม่?', false);
    if (confirmed) {
        window.location.href = 'ajax/projects/ApproveClosure.php?id=' + id;
    }
}
