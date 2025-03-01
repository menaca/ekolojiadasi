<?php

session_start();
require '../netting/connect.php';


$adminId = $_SESSION['admin_id']; 


$stmt = $conn->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->bind_param("i", $adminId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $adminData = $result->fetch_assoc();
    $username = $adminData['username'];
    $name = $adminData['name'];
    $role = $adminData['role'];
    $profile_picture = $adminData['profile_picture'];
    $hashedPassword = $adminData['password']; 
} else {
    echo "Admin bilgileri bulunamadı.";
    exit();
}

$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['profile_update'])) {
    $newName = $_POST['name'];
    $newProfilePicture = $_FILES['profile_picture'];

    if ($newProfilePicture['name']) {
        $targetDir = "../uploads/users_profile/";
        $fileExtension = pathinfo($newProfilePicture["name"], PATHINFO_EXTENSION); 
        $newFileName = uniqid("profile_") . "." . $fileExtension; 
        $targetFile = $targetDir . $newFileName;
    
        if (move_uploaded_file($newProfilePicture["tmp_name"], $targetFile)) {
            $newProfilePictureName = $newFileName; 
        } else {
            echo "Fotoğraf yükleme hatası!";
            $newProfilePictureName = $profile_picture; 
        }
    } else {
        $newProfilePictureName = $profile_picture;
    }

    $updateProfileStmt = $conn->prepare("UPDATE admins SET name = ?, profile_picture = ? WHERE id = ?");
    $updateProfileStmt->bind_param("ssi", $newName, $newProfilePictureName, $adminId);

    if ($updateProfileStmt->execute()) {
        header("Location: edit_profile.php?status=ok");
    } else {
        echo "Hata: " . $updateProfileStmt->error;
    }

    $updateProfileStmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['password_update'])) {
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];

    if (password_verify($oldPassword, $hashedPassword)) {
        $newHashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        $updatePasswordStmt = $conn->prepare("UPDATE admins SET password = ? WHERE id = ?");
        $updatePasswordStmt->bind_param("si", $newHashedPassword, $adminId);

        if ($updatePasswordStmt->execute()) {
            header("Location: admin_list.php?status=password_updated");
        } else {
            echo "Hata: " . $updatePasswordStmt->error;
        }

        $updatePasswordStmt->close();
    } else {
        echo "Eski şifre yanlış!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<body>
<style>
        .account-settings .user-profile {
            margin: 0 0 1rem 0;
            padding-bottom: 1rem;
            text-align: center;
        }
        .account-settings .user-profile .user-avatar {
            margin: 0 0 1rem 0;
        }
        .account-settings .user-profile .user-avatar img {
            width: 90px;
            height: 90px;
            border-radius: 100px;
        }
        .account-settings .user-profile h5.user-name {
            margin: 0 0 0.5rem 0;
        }
        .account-settings .user-profile h6.user-email {
            margin: 0;
            font-size: 0.8rem;
            font-weight: 400;
            color: #9fa8b9;
        }
        .account-settings .about {
            margin: 2rem 0 0 0;
            text-align: center;
        }
        .account-settings .about h5 {
            margin: 0 0 15px 0;
            color: #007ae1;
        }
        .account-settings .about p {
            font-size: 0.825rem;
        }
        .form-control {
            border: 1px solid #cfd1d8;
            border-radius: 2px;
            font-size: .825rem;
            background: #ffffff;
            color: #2e323c;
        }
        .card {
            background: #ffffff;
            border-radius: 5px;
            border: 0;
            margin-bottom: 1rem;
        }
    </style>


    <div id="wrapper">
        <?php include 'widgets/header.php'; include 'widgets/sidebar.php'; ?>

        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-head-line">Hesabınızı Düzenleyin</h1>
                        <h1 class="page-subhead-line">Hesabınız paylaştığınız bloglarda herkese açık gösterilir.</h1>
                    </div>
                </div>

                <div class="container">
                    <!-- Profil Güncelleme Formu -->
                    <div class="row gutters">
                        <div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="account-settings">
                                        <div class="user-profile">
                                            <div class="user-avatar">
                                                <img style="object-fit: cover;"src="../uploads/users_profile/<?php echo $profile_picture; ?>" alt="Admin Avatar">
                                            </div>
                                            <h5 class="user-name"><?php echo $name; ?></h5>
                                            <h6 class="user-email"><?php echo $username; ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-9 col-lg-9 col-md-12 col-sm-12 col-12">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="mb-2 text-primary">Profil Güncelleme</h6>
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label>İsim: </label>
                                            <input class="form-control" type="text" name="name" value="<?php echo $name; ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label>Profil Fotoğrafı</label>
                                            <input class="form-control" type="file" name="profile_picture">
                                        </div>

                                        <input type="submit" name="profile_update" class="btn btn-info" value="Profil Güncelle">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Şifre Güncelleme Formu -->
                    <div class="row gutters">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="mb-2 text-primary">Şifre Güncelleme</h6>
                                    <form method="POST">
                                        <div class="form-group">
                                            <label>Eski Şifre</label>
                                            <input class="form-control" type="password" name="old_password" required>
                                        </div>

                                        <div class="form-group">
                                            <label>Yeni Şifre</label>
                                            <input class="form-control" type="password" name="new_password" required>
                                        </div>

                                        <input type="submit" name="password_update" class="btn btn-primary" value="Şifre Güncelle">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'widgets/footer.php'; ?>
    </div>
</body>
</html>
