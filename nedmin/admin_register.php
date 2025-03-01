<?php

session_start();

require '../netting/connect.php';

if ($_SESSION['admin_role'] != "admin") {
    header("Location: index.php?status=yekisiz"); 
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];
    $name = $_POST['name'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO admins (username, name, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $name, $hashed_password,$role);

    if ($stmt->execute()) {
        header("Location: admin_list.php?status=ok");
    } else {
        echo "Hata: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

    <!DOCTYPE html>
    <html xmlns="http://www.w3.org/1999/xhtml">

    <body>
        <div id="wrapper">

            <?php include 'widgets/header.php';
            include 'widgets/sidebar.php'; ?>


            <div id="page-wrapper">
                <div id="page-inner">
                    <div class="row">
                        <div class="col-md-12">
                            <h1 class="page-head-line">EKİBİNİZİ BÜYÜTÜN</h1>
                            <h1 class="page-subhead-line">Yönetim Ekibine yeni birini ekliyorsunuz. </h1>

                        </div>
                    </div>


                    <div class="col-md-6">

                        <form method="POST" enctype="multipart/form-data">

                            <div class="form-group">
                                <label>Kullanıcı Adı: </label>
                                <input class="form-control" type="text" name="username" required>
                            </div>

                            <div class="form-group">
                                <label>İsim: </label>
                                <input class="form-control" type="text" name="name" required>
                            </div>
                            <div class="form-group">
                                <label>Şifre</label>
                                <input class="form-control" type="password" name="password" required><br>
                            </div>

                            <div class="form-group">
                                <label>Rolünü Seçin</label>
                                <select class="form-control" name="role" required>
                                    <option value="yazar">Yazar</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>

                            <input type="submit" class="btn btn-info" value="Kaydet"></input>
                        </form>
                    </div>


                </div>




            </div>
        </div>

        <?php include 'widgets/footer.php'; ?>

    </body>

    </html>