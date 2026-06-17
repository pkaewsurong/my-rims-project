    </div><!-- /.page-content -->
</div><!-- /.main-wrapper -->

<!-- ===== SCRIPTS ===== -->
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>
<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Toastr -->
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css">

<!-- Page-specific JS -->
<?php if (isset($pageJs) && $pageJs): ?>
<script src="js/<?php echo htmlspecialchars($pageJs); ?>.js"></script>
<?php endif; ?>

<script>
    // Sidebar toggle (mobile)
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('show');
    }

    // Notification dropdown toggle
    function toggleNotif() {
        const dropdown = document.getElementById('notifDropdown');
        dropdown.classList.toggle('show');
    }

    // Close notification when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('notifDropdown');
        const btn = document.getElementById('notifBtn');
        if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });

    // Logout confirm
    function confirmLogout() {
        Swal.fire({
            title: 'ออกจากระบบ',
            text: 'คุณต้องการออกจากระบบใช่หรือไม่?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'ใช่, ออกจากระบบ',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6c757d',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'login.php?action=logout';
            }
        });
    }

    // Global AJAX helper functions
    function showAlert(msg, type = 'success') {
        Swal.fire({
            text: msg,
            icon: type,
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }

    function showConfirm(title, msg, isDanger = false) {
        return Swal.fire({
            title: title,
            html: msg,
            icon: isDanger ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: isDanger ? '#ef4444' : '#191a23',
            cancelButtonColor: '#6c757d',
        }).then(r => r.isConfirmed);
    }

    // Initialize Select2 globally
    $(document).ready(function() {
        // Toastr config
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 3000
        };
    });
</script>
</body>
</html>
