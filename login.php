<?php
// Memulai session
session_start();

require 'koneksi.php';

// Cek apakah user sudah login
if (isset($_SESSION['login'])) {
  header('Location: index.php');
  exit;
}

if (isset($_POST['login'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Mencari username di database
  $stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM users WHERE username = ?"
  );

  mysqli_stmt_bind_param($stmt, "s", $username);
  mysqli_stmt_execute($stmt);

  $result = mysqli_stmt_get_result($stmt);

  // Cek apakah username ditemukan
  if (mysqli_num_rows($result) === 1) {
    $row = mysqli_fetch_assoc($result);

    // Cek apakah password benar
    if (password_verify($password, $row['password'])) {

      // Set session
      $_SESSION['login'] = true;
      $_SESSION['username'] = $username;
      $_SESSION['role'] = $row['user_role'];

      // Login berhasil
      header('Location: index.php');
      exit;
    }
  }

  // Login gagal
  $error = true;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>Halaman Login</title>

  <!-- Font Awesome -->
  <link href="assets/vendor/fontawesome-free/css/all.min.css"
    rel="stylesheet" type="text/css">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
    rel="stylesheet">

  <!-- CSS SB Admin 2 -->
  <link href="assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary">

  <div class="container">

    <?php if (isset($error)) : ?>
      <div class="alert alert-danger mt-3" role="alert">
        Username atau password salah!
      </div>
    <?php endif; ?>

    <!-- Outer Row -->
    <div class="row justify-content-center">

      <div class="col-xl-10 col-lg-12 col-md-9">

        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">

            <div class="row">

              <!-- Gambar Login -->
              <div class="col-lg-6 d-none d-lg-block bg-login-image">
                <img src="assets/images/login-page.png"
                  alt="Gambar Login"
                  class="img-fluid">
              </div>

              <!-- Form Login -->
              <div class="col-lg-6">
                <div class="p-5">

                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">
                      Welcome Back!
                    </h1>
                  </div>

                  <form method="post" action="" class="user">

                    <div class="form-group">
                      <input
                        type="text"
                        class="form-control form-control-user"
                        id="username"
                        name="username"
                        placeholder="Username ..."
                        required>
                    </div>

                    <div class="form-group">
                      <input
                        type="password"
                        class="form-control form-control-user"
                        id="password"
                        name="password"
                        placeholder="Password ..."
                        required>
                    </div>

                    <div class="form-group">
                      <div class="custom-control custom-checkbox small">
                        <input
                          type="checkbox"
                          class="custom-control-input"
                          id="remember"
                          name="remember">

                        <label
                          class="custom-control-label"
                          for="remember">
                          Remember Me
                        </label>
                      </div>
                    </div>

                    <button
                      type="submit"
                      name="login"
                      class="btn btn-primary btn-user btn-block">
                      Login
                    </button>

                  </form>

                  <hr>

                  <div class="text-center">
                    <a class="small" href="forgot-password.html">
                      Forgot Password?
                    </a>
                  </div>

                </div>
              </div>

            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- JavaScript -->
  <script src="assets/vendor/jquery/jquery.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="assets/js/sb-admin-2.min.js"></script>

</body>

</html>