<?php 

session_start();

require '../netting/connect.php';

$error_text = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin) {
        if ($admin['status'] == 'pasif') {
            $error_text = "Hesabınız blokeli! Lütfen yöneticinizle iletişime geçin.";
        } elseif (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id']; 
            $_SESSION['admin_name'] = $admin['name'];       
            $_SESSION['admin_role'] = $admin['role'];  

            header("Location: index.php");
        } else {
            $error_text = "Kullanıcı adı veya şifre yanlış!";
        }
    } else {
        $error_text = "Kullanıcı adı veya şifre yanlış!";
    }

    $stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EKOLOJİ ADASI ADMİN PANELİ</title>

  <link href="assets/css/bootstrap.css" rel="stylesheet" />
  <link href="assets/css/font-awesome.css" rel="stylesheet" />
  <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />

  <style>
  </style>
</head>
<body>
    <div class="container">
        <div class="row text-center " style="padding-top:100px;">
            <div class="col-md-12">
                <img width="150" src="/images/logo.png" />
            </div>
        </div>
        <div class="row ">

            <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1">

                <div class="panel-body">
                    <form method="POST" role="form">
                        <hr />
                        <h5>Admin Panele Giriş Yapın</h5>
                        <br />

                        <?php if(strlen($error_text) > 1): ?>
                      <div class="alert alert-danger" role="alert">
                            <?php  echo  $error_text?>
                      </div>
                  <?php endif;?>   

                        <div class="form-group input-group">
                            <span class="input-group-addon"><i class="fa fa-tag" ></i></span>
                            <input type="text" class="form-control" placeholder="Kullanıcı Adınız" name="username" />
                        </div>
                        <div class="form-group input-group">
                            <span class="input-group-addon"><i class="fa fa-lock" ></i></span>
                            <input type="password" class="form-control" placeholder="Şifreniz" name="password" />
                        </div>

                         <button type="submit" class="btn btn-primary ">Giriş</button>
                     <hr />
                 </form>
             </div>

         </div>


     </div>
 </div>

</body>
</html>