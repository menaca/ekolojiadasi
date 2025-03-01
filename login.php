<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit();
}



require 'netting/connect.php';

$alert_text = '';
$alert_type = '';

if (isset($_GET['status']) && $_GET['status'] == 'ok') {
    $alert_text = 'Kayıt Oluşturuldu!';
    $alert_type = 'success';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email_or_username = mysqli_real_escape_string($conn, trim($_POST['email_or_username']));
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM users WHERE email = '$email_or_username' OR username = '$email_or_username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        if ($user['status'] == 'pasif') {
          $alert_text = "Hesabınız yasaklandı. Yanlışlık olduğunu düşünüyorsanız lütfen yöneticilerle iletişime geçin.";
          $alert_type = 'danger';
      } elseif (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: profile.php");
            exit();
        } else {
            $alert_text = "Giriş verileri yanlış!";
            $alert_type = 'danger';

        }
    } else {
        $alert_text = "Giriş verileri yanlış!";
        $alert_type = 'danger';

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

<section class="vh-100" style="background-color: #014509">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col col-xl-5">
        <div class="card" style="border-radius: 1rem;">
          <div class="row g-0">

            <div class="col-md-6 col-lg-12 d-flex align-items-center">
              <div class="card-body p-4 p-lg-5 text-black">

              <form action="login.php" method="POST">


                  <div class="d-flex align-items-center mb-3 pb-1">
                  <a href="index.php"><img src="images/logo.png"
                  alt="login form" class="img-fluid" style="width: 75px;height=75px;" /></a>
                  </div>



                  <h5 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Hesabınıza Giriş Yapın</h5>
                  <?php if(strlen($alert_text) > 1): ?>
                      <div class="alert alert-<?php  echo  $alert_type?>" role="alert">
                            <?php  echo  $alert_text?>
                      </div>
                  <?php endif;?>    
                  <div data-mdb-input-init class="form-outline mb-4">
                    <input id="form2Example17" class="form-control form-control-lg" type="text" name="email_or_username" placeholder="Kullanıcı Adı veya E-posta" required />
                  </div>

                  <div data-mdb-input-init class="form-outline mb-4">
                    <input type="password" name="password" placeholder="Şifre" required id="form2Example27" class="form-control form-control-lg" />
                  </div>

                  <div class="pt-1 mb-4">
                  <input class="btn btn-dark btn-lg btn-block" type="submit" value="Giriş Yap">
                  </div>

                  <p class="mb-5 pb-lg-2" style="color: #393f81;">Hesabınız yok mu? <a href="register.php"
                      style="text-color: #393f81;">Hemen Kaydolun</a></p>
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
