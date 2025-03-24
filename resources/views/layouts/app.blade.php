<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sirkulasi Stok')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
        }

        @media (max-width: 992px) {
            .content {
                margin-left: 0;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    @include('partials.navbar')

    <div class="d-flex">
        @include('partials.sidebar')

        <div class="content flex-grow-1">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    <script>
        document.getElementById("toggleSidebar").addEventListener("click", function () {
            let sidebar = document.getElementById("sidebar");
            if (sidebar.style.display === "none" || sidebar.classList.contains("d-none")) {
                sidebar.style.display = "block";
                sidebar.classList.remove("d-none");
            } else {
                sidebar.style.display = "none";
                sidebar.classList.add("d-none");
            }
        });

        document.querySelectorAll('.navbar-toggler').forEach(function (toggler) {
            toggler.addEventListener('click', function () {
                let target = document.querySelector(this.getAttribute('data-bs-target'));
                target.classList.toggle('show');
            });
        });
    </script>
</body>

</html>