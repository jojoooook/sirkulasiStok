<nav class="navbar navbar-expand-lg navbar-dark"
    style="background-color: #2C3E50; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">

    <div class="container-fluid">
        <button class="btn btn-light me-2 d-lg-none" id="toggleSidebar">
            <i class="fas fa-bars"></i>
        </button>

        <a class="navbar-brand" href="#">Sirkulasi Stok</a>

        <button class="navbar-toggler" type="button" id="navbarToggler">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                @auth
                    <li class="nav-item d-flex align-items-center">
                        <span class="nav-link text-white">
                            <i class="fas fa-user"></i> {{ Auth::user()->name }} ({{ Auth::user()->role }})
                        </span>
                    </li>
                @endauth
                @guest
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('login') }}">Login</a>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let navbarToggler = document.getElementById("navbarToggler");
        let navbarCollapse = document.getElementById("navbarNav");

        navbarToggler.addEventListener("click", function () {
            navbarCollapse.classList.toggle("show");
        });

        document.addEventListener("click", function (event) {
            let isClickInsideNavbar = navbarCollapse.contains(event.target);
            let isClickToggler = navbarToggler.contains(event.target);

            if (!isClickInsideNavbar && !isClickToggler) {
                navbarCollapse.classList.remove("show");
            }
        });
    });
</script>