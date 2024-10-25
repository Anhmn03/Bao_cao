<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">SB User</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="{{ route('attendance') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Báo cáo chấm công</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <li class="nav-item">
        <a class="nav-link" href="{{ route('attendance.monthlyReport') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Quản lý chấm công</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
<script>
    $(document).ready(function() {
    // Khi một mục menu được nhấp vào
    $('.nav-link').on('click', function() {
        // Bỏ active khỏi tất cả các mục
        $('.nav-item').removeClass('active');
        // Thêm active cho mục đang được nhấp
        $(this).parent('.nav-item').addClass('active');
    });
});
</script>