<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Research Paper Repository</title>
  <!-- Enhanced Stylesheets -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link href="css/theme.css" rel="stylesheet">
  <link href="css/navigation.css" rel="stylesheet">
  <link href="css/cards.css" rel="stylesheet">
  <link href="css/header.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <script src="js/header.js" defer></script>
</head>

<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit(); 
}
?>
<body>
  <header>
    <nav class="navbar navbar-expand-lg">
      <div class="container">
        <a class="navbar-brand" href="#">
          <i class="fas fa-book-reader me-2"></i>
          Research Paper Repository
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <?php 
          $path_parts = pathinfo($_SERVER['PHP_SELF']);
          $user_id = $_SESSION['role'];
          ?>

          <ul class="navbar-nav ms-auto">
              <?php if ($user_id == 1): ?>
                  <li class="nav-item">
                      <a class="nav-link <?php echo ($path_parts['basename'] == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">
                          <i class="fas fa-home me-2"></i>Home
                      </a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link <?php echo ($path_parts['basename'] == 'users.php') ? 'active' : ''; ?>" href="users.php">
                          <i class="fas fa-users me-2"></i>Users
                      </a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link <?php echo ($path_parts['basename'] == 'papers.php') ? 'active' : ''; ?>" href="papers.php">
                          <i class="fas fa-file-alt me-2"></i>Papers
                      </a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link <?php echo ($path_parts['basename'] == 'favorite.php') ? 'active' : ''; ?>" href="favorite.php">
                          <i class="fas fa-heart me-2"></i>Favorites
                      </a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" href="#" id="logout">
                          <i class="fas fa-sign-out-alt me-2"></i>Log Out
                      </a>
                  </li>
              <?php elseif ($user_id == 2): ?>
                  <li class="nav-item">
                      <a class="nav-link <?php echo ($path_parts['basename'] == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">
                          <i class="fas fa-home me-2"></i>Home
                      </a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link <?php echo ($path_parts['basename'] == 'papers.php') ? 'active' : ''; ?>" href="papers.php">
                          <i class="fas fa-file-alt me-2"></i>Papers
                      </a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link <?php echo ($path_parts['basename'] == 'favorite.php') ? 'active' : ''; ?>" href="favorite.php">
                          <i class="fas fa-heart me-2"></i>Favorites
                      </a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" href="#" id="logout">
                          <i class="fas fa-sign-out-alt me-2"></i>Log Out
                      </a>
                  </li>
              <?php elseif ($user_id == 3): ?>
                  <li class="nav-item">
                      <a class="nav-link <?php echo ($path_parts['basename'] == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">
                          <i class="fas fa-home me-2"></i>Home
                      </a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link <?php echo ($path_parts['basename'] == 'favorite.php') ? 'active' : ''; ?>" href="favorite.php">
                          <i class="fas fa-heart me-2"></i>Favorites
                      </a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" href="#" id="logout">
                          <i class="fas fa-sign-out-alt me-2"></i>Log Out
                      </a>
                  </li>
              <?php endif; ?>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
<?php if (isset($_SESSION['user_id']) && isset($_SESSION['role'])): ?>
    <input type="hidden" id="userId" value="<?php echo $_SESSION['user_id']; ?>">
    <input type="hidden" id="userRole" value="<?php echo $_SESSION['role']; ?>">
<?php endif; ?>