<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">SB Admin <sup>2</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
   
    <hr class="sidebar-divider">

    <li class="nav-item active">
        <a class="nav-link" href="{{ route('departments') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span >Phòng ban</span></a>
    </li>
    <hr class="sidebar-divider">

    <li class="nav-item active">
        <a class="nav-link" href="{{ route('users') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span >Nhân viên</span></a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">
<li class="nav-item active">
    <a class="nav-link" href="{{ route('department.report') }}">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Quản lý chấm công</span>
    </a>
    <!-- Faded submenu items -->
    <ul class="nav flex-column pl-4" style="opacity: 0.5;">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('department.report') }}">
                <span>Báo cáo</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.manageAttendances') }}">
                <span>Chấm công không hợp lệ</span>
            </a>
        </li>
    </ul>
</li>

    
    <hr class="sidebar-divider d-none d-md-block">
    <li class="nav-item active">
        <a class="nav-link" href="{{ route('salary') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span >Quản lý bậc lương </span></a>
    </li>

    {{-- <hr class="sidebar-divider d-none d-md-block">
    <li class="nav-item active">
        <a class="nav-link" href="{{ route('attendance.all') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span >Thử</span></a>
    </li>
    --}}
    {{-- <li class="nav-item active">
        <a class="nav-link" href="{{ route('attendance.all') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span >Quản lý chấm công </span></a>
    </li> --}}

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
   
    <!-- Nav Item - Pages Collapse Menu -->
   

    <!-- Nav Item - Utilities Collapse Menu -->
   

    <!-- Divider -->

    <!-- Heading -->
    
    <!-- Nav Item - Pages Collapse Menu -->
   
    <!-- Nav Item - Charts -->
   
    <!-- Divider -->
    

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>