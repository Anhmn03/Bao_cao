<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

   

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">

        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
        <li class="nav-item dropdown no-arrow d-sm-none">
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
            </a>
            <!-- Dropdown - Messages -->
            
        </li>

        <!-- Nav Item - Alerts -->
        

        <!-- Nav Item - Messages -->
        

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ auth()->user()->name ?? 'Khách' }}
                </span>
                <img class="img-profile rounded-circle"
                    src="fe-access/img/undraw_profile.svg">
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">
                @if (auth()->user() && auth()->user()->role == 2)
                <a class="dropdown-item"href="{{ route('users.show', Auth::user()->id) }}">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Thông tin cá nhân 
                </a>
            @endif
                
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" 
   onclick="return confirmLogout();">
    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
    Đăng xuất
</a>


            </div>
        </li>


    </ul>
    <script>
        function confirmLogout() {
            if (confirm("Bạn có chắc chắn muốn đăng xuất không?")) {
                window.location.href = "{{ route('logout') }}";
            }
            return false; // Ngăn chặn hành động mặc định nếu nhấn Cancel
        }
    </script>
</nav>