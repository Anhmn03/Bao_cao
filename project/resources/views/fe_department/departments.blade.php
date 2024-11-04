<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Admin</title>

    <!-- Custom fonts for this template-->
    <link href="/fe-access/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="/fe-access/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        .tree { padding-left: 20px; }
        .toggle-node { cursor: pointer; display: flex; align-items: center; margin-bottom: 5px; }
        .child-department { margin-left: 30px; }
        .toggle-btn { margin-right: 10px; }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        @include('fe_admin.slidebar')

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('fe_admin.topbar')

                <div class="container-fluid">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Danh sách phòng ban</h1>
                        <div class="d-flex">
                            <a href="{{ route('departments.create') }}" class="btn btn-primary mr-2">Thêm phòng ban</a>

                            <form action="{{ route('departments.search') }}" method="GET" class="form-inline">
                                <div class="input-group">
                                    <input type="text" name="query" class="form-control" placeholder="Tìm kiếm phòng ban" required aria-label="Search" aria-describedby="search-button">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit" id="search-button">Tìm kiếm</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="tree">
                            @if($departments->isEmpty())
                                <p>Không tìm thấy phòng ban nào khớp với từ khóa.</p>
                            @else
                            @foreach ($departments as $department)
                            @include('fe_department.department_tree', ['department' => $department])
                        @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>© {{ date('Y') }} Your Company. All Rights Reserved.</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleButtons = document.querySelectorAll('.toggle-btn');

            toggleButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.parentElement.getAttribute('data-id');
                    const subTree = document.querySelector(`.sub-departments-${id}`);
                    const icon = this.querySelector('i');

                    // Toggle the display of the subtree
                    const isHidden = subTree.style.display === 'none';
                    subTree.style.display = isHidden ? 'block' : 'none';

                    // Toggle the icon class between up and down chevron
                    icon.classList.toggle('fa-chevron-down', !isHidden);
                    icon.classList.toggle('fa-chevron-up', isHidden);
                });
            });
        });
    </script>

    <script src="/fe-access/vendor/jquery/jquery.min.js"></script>
    <script src="/fe-access/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/fe-access/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="/fe-access/js/sb-admin-2.min.js"></script>
</body>

</html>