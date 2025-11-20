<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top py-2">
  <div class="container-fluid px-4 d-flex align-items-center">

    <!-- LOGO KIRI -->
    <a class="navbar-brand d-flex align-items-center fw-bold text-primary me-auto"
       href="<?= base_url('dashboard'); ?>"
       style="font-size: 1.25rem;">
      <img src="<?= base_url('assets/img/hotel.png') ?>"
           alt="Logo"
           style="width: 32px; height: 32px; margin-right: 10px;">
      Safeguard Hotel
    </a>

    <!-- MENU TENGAH -->
    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
      <ul class="navbar-nav gap-3">

        <li class="nav-item">
          <a class="nav-link px-2" href="<?= base_url('leaderboard'); ?>">Dashboard</a>
        </li>

        <li class="nav-item">
          <a class="nav-link px-2" href="<?= base_url('dashboard'); ?>">Booking</a>
        </li>

        <li class="nav-item">
          <a class="nav-link px-2" href="<?= base_url('dashboard'); ?>">Check-in</a>
        </li>

        <li class="nav-item">
          <a class="nav-link px-2" href="<?= base_url('customerservice'); ?>">Rooms</a>
        </li>

        <li class="nav-item">
          <a class="nav-link px-2" href="<?= base_url('reports'); ?>">Reports</a>
        </li>

      </ul>
    </div>

    <!-- LOGOUT PALING KANAN -->
    <a class="btn btn-outline-danger rounded-pill px-3 ms-auto"
       href="<?= base_url('auth/logout'); ?>">
      Logout
    </a>

    <!-- TOGGLER -->
    <button class="navbar-toggler ms-3" type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

  </div>
</nav>
