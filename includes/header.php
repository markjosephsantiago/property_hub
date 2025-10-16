<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Property Hub</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
    body { font-family: Arial, sans-serif; }
    .navbar { background: red; }
    .navbar a { color: white !important; font-weight: bold; }
    .hero {
      background: url('dist/img/photo4.jpg') no-repeat center center;
      background-size: cover;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      text-align: center;
      position: relative;
    }
    .hero::before {
      content: "";
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
    }
    .hero-content {
      position: relative;
      z-index: 2;
    }
    .hero h1 { font-size: 4rem; font-weight: bold; }
    .hero p { font-size: 1.5rem; }
    .btn-login {
      background: yellow;
      color: black;
      font-weight: bold;
      padding: 10px 30px;
      border-radius: 30px;
      margin-top: 20px;
      text-decoration: none;
    }
    .dropdown-menu {
      background-color: red;
      border: none;
      border-radius: 0; 
    }

    .dropdown-menu .dropdown-item {
      color: white;  
      font-weight: bold;
    }

    .dropdown-menu .dropdown-item:hover {
      background-color: yellow;
      color: black;
    }
    section { padding: 60px 0; }
    .section-title { text-align: center; margin-bottom: 40px; font-weight: bold; }
    footer { background: #111; color: white; padding: 20px; text-align: center; }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top" style="background:red;">
  <div class="container">
    <a class="navbar-brand text-white fw-bold" href="home.php">PROPERTY HUB</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="../../home.php">HOME</a></li>
        <li class="nav-item"><a class="nav-link" href="aboutus.php">ABOUT US</a></li>
        <li class="nav-item"><a class="nav-link" href="facilities.php">FACILITIES</a></li>
        <li class="nav-item"><a class="nav-link" href="gallery.php">GALLERY</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">CONTACT</a></li>

        <?php if (isset($_SESSION['role'])): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle btn-login" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown">
              <?= htmlspecialchars($_SESSION['fullname']) ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="../dashboard/index.php">Dashboard</a></li>
              <li><a class="dropdown-item" href="../login/usersData/ctrl.logout.php">Logout</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link btn-login" href="../pages/login/login.php">LOGIN</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
