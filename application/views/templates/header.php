<?php 
   if (!$this->session->userdata('logged_in')) {
      redirect('login');
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= isset($title) ? $title : 'Hotel Management'; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    html, body {
      height: 100%;
            background-color: white;

    }

    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      background-color: white;
    }

    main {
      flex: 1;
      padding-top: 90px; /* kasih jarak dari navbar */
      padding-bottom: 30px;
      background-color: white;
    }

    footer {
      background-color: #f8f9fa;
      border-top: 1px solid #dee2e6;
      padding: 15px 0;
      text-align: center;
      color: :white;
      width: 100%;
    }
 .navbar-nav .nav-link {
    font-weight: 500;
    color: #555 !important;
    transition: 0.2s ease;
  }

  .navbar-nav .nav-link:hover {
    color: #006699 !important;
  }

  .navbar-nav .nav-link.active {
    color: #006699 !important;
    font-weight: 600;
    border-bottom: 2px solid #006699;
  }

  .navbar {
    backdrop-filter: blur(8px);
    }
  </style>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
