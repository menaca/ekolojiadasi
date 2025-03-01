<?php

session_start(); 

require '../netting/connect.php';

if ($_SESSION['admin_role'] != "admin") {
    header("Location: index.php?status=yekisiz"); 
    exit();
}


$status = '';

if (isset($_GET['status']) && $_GET['status'] == 'ok') {
    $status = 'Ekip üyesi eklendi!';
}

$query_admins = "SELECT * FROM admins WHERE role = 'admin' ORDER BY id";
$query_authors = "SELECT * FROM admins WHERE role = 'yazar' ORDER BY id";

$admins_result = mysqli_query($conn, $query_admins);
$authors_result = mysqli_query($conn, $query_authors);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Durum güncelleme işlemi
    if (isset($_POST['id']) && isset($_POST['status'])) {
        $userId = $_POST['id'];
        $newStatus = $_POST['status'];

        // Status'u güncelleme sorgusu
        $query = "UPDATE admins SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'si', $newStatus, $userId);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            $status = 'Durum Değiştirildi!';
       } else {
            echo "Bir hata oluştu.";
        }
        exit();
    }
}

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<body>
    <div id="wrapper">

    <?php include'widgets/header.php'; include 'widgets/sidebar.php';?>


        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-head-line">Yönetim Ekibi</h1>
                        <h1 class="page-subhead-line">Yönetim Ekibi Listelerini kontrol edin düzenleyin. </h1>
                    </div>
                </div>
                
                <?php if($status != null) echo "<div class='alert alert-success'>$status</div>"; ?>

                <a href="admin_register.php">
                <button  class='btn btn-warning'>Yönetici Ekle</button></a>

                <div class="panel panel-default">
                    
                    <div class="panel-heading">
                        Yöneticiler
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive table-bordered">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>#</th>
                                        <th>Kullanıcı Adı</th>
                                        <th>İsmi</th>
                                        <th>Rolü</th>
                                        <th>Kayıt Tarihi</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $admin_counter = 1;
                                    $session_user_id = $_SESSION['admin_id'];
                                    while ($admin = mysqli_fetch_assoc($admins_result)) {

                                        $status_button = ($admin['status'] == 'aktif') 
                                        ? "<button class='btn btn-danger' onclick='toggleStatus({$admin['id']}, \"pasif\")'>Deaktive Et</button>"
                                        : "<button class='btn btn-success' onclick='toggleStatus({$admin['id']}, \"aktif\")'>Aktive Et</button>";


                                        echo "<tr>";
                                        echo "<td>{$admin_counter}</td>";
                                        echo "<td><img src='../uploads/users_profile/{$admin['profile_picture']}' alt='Profil Fotoğrafı' style='width:30px; height:30px; border-radius:50%;'></td>";
                                        echo "<td>{$admin['username']}</td>";
                                        echo "<td>{$admin['name']}</td>";
                                        echo "<td>{$admin['role']}</td>";
                                        echo "<td>{$admin['created_time']}</td>";
                                        echo ($admin['id'] == $session_user_id) ? '<td></td>' : "<td>$status_button</td>";
                                        echo "</tr>";
                                        $admin_counter++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        Yazarlar
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive table-bordered">
                            <table class="table">
                                <thead>
                                    <tr>
                                    <th>#</th>
                                        <th>#</th>
                                        <th>Kullanıcı Adı</th>
                                        <th>İsmi</th>
                                        <th>Rolü</th>
                                        <th>Kayıt Tarihi</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $author_counter = 1;
                                    $session_user_id = $_SESSION['admin_id'];
                                    while ($author = mysqli_fetch_assoc($authors_result)) {

                                        $status_button = ($author['status'] == 'aktif') 
                                        ? "<button class='btn btn-danger' onclick='toggleStatus({$author['id']}, \"pasif\")'>Deaktive Et</button>"
                                        : "<button class='btn btn-success' onclick='toggleStatus({$author['id']}, \"aktif\")'>Aktive Et</button>";

                                        echo "<tr>";
                                        echo "<td>{$author_counter}</td>";
                                        echo "<td><img src='../uploads/users_profile/{$author['profile_picture']}' alt='Profil Fotoğrafı' style='width:30px; height:30px; border-radius:50%;'></td>";
                                        echo "<td>{$author['username']}</td>";
                                        echo "<td>{$author['name']}</td>";
                                        echo "<td>{$author['role']}</td>";
                                        echo "<td>{$author['created_time']}</td>";
                                        echo ($author['id'] == $session_user_id) ? '<td></td>' : "<td>$status_button</td>";
                                        echo "</tr>";
                                        $author_counter++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        

            </div>
        </div>
    </div>

    <?php include'widgets/footer.php';?>
  

    <script>
    function toggleStatus(userId, newStatus, btn) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                location.reload();  
            }
        };
        xhr.send('id=' + userId + '&status=' + newStatus);
    }
    </script>

</body>
</html>




