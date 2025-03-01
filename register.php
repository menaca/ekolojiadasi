<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit();
}

require 'netting/connect.php';

$error_text="";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $real_name = mysqli_real_escape_string($conn, trim($_POST['real_name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = password_hash(trim($_POST['password']), algo: PASSWORD_BCRYPT);  

    $sql_check = "SELECT * FROM users WHERE email = '$email' OR username = '$username'";
    $result_check = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($result_check) > 0) {
        $error_text= "Kullanıcı adı ya da e-posta zaten alınmış.";
    } else {
        $sql = "INSERT INTO users (username, real_name, email, password) VALUES ('$username', '$real_name', '$email', '$password')";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: login.php?status=ok");
        } else {
            $error_text= "Bir hata oluştu: " . mysqli_error($conn);
        }
    }
    
    mysqli_close($conn);
}
?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Giriş Yap</title>
</head>
<body>

<section class="vh-80" style="background-color: #014509">
  <div class="container py-5 h-80">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col col-xl-5">
        <div class="card" style="border-radius: 1rem;">
          <div class="row g-0">

            <div class="col-md-6 col-lg-12 d-flex align-items-center">
              <div class="card-body p-4 p-lg-5 text-black">

              <form action="register.php" method="POST">


                  <div class="d-flex align-items-center mb-3 pb-1">
                  <a href="index.php"><img src="images/logo.png"
                  alt="login form" class="img-fluid" style="width: 75px;height=75px;" /></a>
                  </div>



                  <h5 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Yeni Hesap Oluşturun</h5>
                  <?php if(strlen($error_text) > 1): ?>
                      <div class="alert alert-danger" role="alert">
                            <?php  echo  $error_text?>
                      </div>
                  <?php endif;?>    
                  <div data-mdb-input-init class="form-outline mb-4">
                    <input class="form-control form-control-lg" type="text" name="username" placeholder="Kullanıcı Adınızı Seçin" required />
                  </div>
                  
                  <div data-mdb-input-init class="form-outline mb-4">
                    <input  class="form-control form-control-lg" type="text" name="real_name" placeholder="Adınızı Girin" required />
                  </div>
                  
                  <div data-mdb-input-init class="form-outline mb-4">
                    <input  class="form-control form-control-lg" type="email" name="email" placeholder="E-postanızı Girin" required />
                  </div>
                  
                  <div data-mdb-input-init class="form-outline mb-4">
                    <input type="password" name="password" placeholder="Şifre Seçin" required class="form-control form-control-lg" />
                  </div>

                  <div class="pt-1 mb-4">
                  <input class="btn btn-dark btn-lg btn-block" type="submit" value="Giriş Yap">
                  </div>

                  <p class="mb-5 pb-lg-2" style="color: #393f81;">Zaten hesabınız var mı? <a href="login.php"
                      style="text-color: #393f81;">Hemen Giriş Yapın</a></p>
                </form>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
</body>
</html>



