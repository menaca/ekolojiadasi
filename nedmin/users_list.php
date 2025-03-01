<?php

session_start();

require '../netting/connect.php';

if ($_SESSION['admin_role'] != "admin") {
    header("Location: index.php?status=yekisiz");
    exit();
}

$status = '';

$query_active_users = "SELECT * FROM users WHERE status = 'aktif' ORDER BY id DESC";
$query_banned_users = "SELECT * FROM users WHERE status = 'pasif' ORDER BY id DESC";

$active_users_result = mysqli_query($conn, $query_active_users);
$banned_users_result = mysqli_query($conn, $query_banned_users);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id']) && isset($_POST['status'])) {
        $userId = $_POST['id'];
        $newStatus = $_POST['status'];

        $query = "UPDATE users SET status = ? WHERE id = ?";
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

        <?php include 'widgets/header.php'; include 'widgets/sidebar.php'; ?>

        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-head-line">Kullanıcı Listesi</h1>
                        <h1 class="page-subhead-line">Aktif ve Pasif Kullanıcıları kontrol edin, düzenleyin.</h1>
                    </div>
                </div>

                <?php if ($status != null) echo "<div class='alert alert-success'>$status</div>"; ?>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        Aktif Kullanıcılar
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
                                        <th>Puan</th>
                                        <th>Kayıt Tarihi</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $active_counter = 1;
                                    while ($user = mysqli_fetch_assoc($active_users_result)) {
                                        echo "<tr>";
                                        echo "<td>{$active_counter}</td>";
                                        echo "<td><img src='/uploads/users_profile/{$user['photo_url']}' alt='Profil Fotoğrafı' style='width:30px; height:30px; border-radius:50%;'></td>";
                                        echo "<td>{$user['username']}</td>";
                                        echo "<td>{$user['real_name']}</td>";
                                        echo "<td>{$user['point']}</td>";
                                        echo "<td>{$user['created_time']}</td>";
                                        echo "<td><button class='btn btn-danger' onclick='toggleStatus({$user['id']}, \"pasif\")'>Banla</button></td>";
                                        echo "</tr>";
                                        $active_counter++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        Banlı Kullanıcılar
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
                                        <th>Puan</th>
                                        <th>Kayıt Tarihi</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $banned_counter = 1;
                                    while ($user = mysqli_fetch_assoc($banned_users_result)) {
                                        echo "<tr>";
                                        echo "<td>{$banned_counter}</td>";
                                        echo "<td><img src='/uploads/users_profile/{$user['photo_url']}' alt='Profil Fotoğrafı' style='width:30px; height:30px; border-radius:50%;'></td>";
                                        echo "<td>{$user['username']}</td>";
                                        echo "<td>{$user['real_name']}</td>";
                                        echo "<td>{$user['point']}</td>";
                                        echo "<td>{$user['created_time']}</td>";
                                        echo "<td><button class='btn btn-success' onclick='toggleStatus({$user['id']}, \"aktif\")'>Banı Kaldır</button></td>";
                                        echo "</tr>";
                                        $banned_counter++;
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

    <?php include 'widgets/footer.php'; ?>

    <script>
    function toggleStatus(userId, newStatus) {
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
