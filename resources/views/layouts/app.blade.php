<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sirkulasi Stok')</title>

    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
        }

        /* Navbar tetap di atas */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1030;
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Sidebar tetap di kiri */
        .sidebar-container {
            position: fixed;
            top: 56px;
            /* tinggi navbar */
            bottom: 0;
            left: 0;
            width: 250px;
            background-color: #f8f9fa;
            border-right: 1px solid #dee2e6;
            overflow-y: auto;
            z-index: 1020;
        }

        /* Konten berada di samping sidebar */
        .content {
            margin-top: 56px;
            /* tinggi navbar */
            margin-left: 250px;
            /* lebar sidebar */
            padding: 20px;
        }

        @media (max-width: 992px) {
            .sidebar-container {
                position: relative;
                top: 0;
                width: 100%;
                height: auto;
                border-right: none;
            }

            .content {
                margin-left: 0;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    @include('partials.navbar')

    <div class="sidebar-container" id="sidebar">
        @include('partials.sidebar')
    </div>

    <div class="content">
        @yield('content')
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
    <script>
        document.getElementById("toggleSidebar")?.addEventListener("click", function () {
            let sidebar = document.getElementById("sidebar");
            if (sidebar.style.display === "none" || sidebar.classList.contains("d-none")) {
                sidebar.style.display = "block";
                sidebar.classList.remove("d-none");
            } else {
                sidebar.style.display = "none";
                sidebar.classList.add("d-none");
            }
        });
    </script>
</body>

</html>