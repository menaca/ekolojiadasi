<?php

session_start(); 

require '../netting/connect.php';

if ($_SESSION['admin_role'] != "admin") {
    header("Location: index.php?status=yekisiz"); 
    exit();
}

$status = '';

if (isset($_GET['status']) && $_GET['status'] && $status == 'ok') {
    $status = 'Durum Değiştirildi!';
}

$query_waiting = "SELECT i.*, u.username FROM images i
                  JOIN users u ON i.user_id = u.id
                  WHERE i.status = 'bekliyor' ORDER BY i.id";
$query_approved = "SELECT i.*, u.username FROM images i
                   JOIN users u ON i.user_id = u.id
                   WHERE i.status = 'onay' ORDER BY i.id";
$query_deny = "SELECT i.*, u.username FROM images i
               JOIN users u ON i.user_id = u.id
               WHERE i.status = 'ret' ORDER BY i.id";


$waiting_result = mysqli_query($conn, $query_waiting);
$approved_result = mysqli_query($conn, $query_approved);
$deny_result = mysqli_query($conn, $query_deny);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id']) && isset($_POST['status'])) {
        $imageId = $_POST['id'];
        $newStatus = $_POST['status'];

        if (in_array($newStatus, ['onay', 'ret', 'bekliyor'])) {
            $query = "UPDATE images SET status = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'si', $newStatus, $imageId);
            $result = mysqli_stmt_execute($stmt);

             if ($result && $newStatus == 'onay') {
                $query_user = "UPDATE users SET point = point + 20 WHERE id = (SELECT user_id FROM images WHERE id = ?)";
                $stmt_user = mysqli_prepare($conn, $query_user);
                mysqli_stmt_bind_param($stmt_user, 'i', $imageId);
                mysqli_stmt_execute($stmt_user);

                $notification_text = "Gönderdiğiniz fotoğraf yayınlandı. 20 puan kazandınız!";
                $notification_type = "point_add";
                $query_notification = "INSERT INTO notifications (user_id, notification_text, notification_type) VALUES ((SELECT user_id FROM images WHERE id = ?), ?, ?)";
                $stmt_notification = mysqli_prepare($conn, $query_notification);
                mysqli_stmt_bind_param($stmt_notification, 'iss', $imageId, $notification_text, $notification_type);
                mysqli_stmt_execute($stmt_notification);

                $status = 'ok';
            } else {
                echo "Bir hata oluştu.";
            }

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
                        <h1 class="page-head-line">Ziyaretçilerden Gelenleri Yayınlayın</h1>
                        <h1 class="page-subhead-line">Ziyaretçilerden Gelenleri İnceleyin ve Onaylayın ya da Silin. </h1>
                    </div>
                </div>
                
                <?php if($status != null) echo "<div class='alert alert-success'>$status</div>"; ?>

                <div class="panel panel-default">
          
                    <div class="panel-heading">
                        Onay Bekleyen Fotoğraflar
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive table-bordered">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Gönderilen Fotoğraf</th>
                                        <th>Açıklama</th>
                                        <th>Gönderen</th>
                                        <th>Gönderilme Tarihi</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $wait_counter = 1;
                                    while ($wait = mysqli_fetch_assoc($waiting_result)) {
                                        echo "<tr>";
                                        echo "<td>{$wait_counter}</td>";
                                        echo "<td><img src='/uploads/users_upload/{$wait['image_name']}' style='width:100px;'></td>";
                                        echo "<td>{$wait['description']}</td>";
                                        echo "<td>{$wait['username']}</td>";
                                        echo "<td>{$wait['created_at']}</td>";
                                        echo "<td><button class='btn btn-success' onclick='toggleStatus({$wait['id']}, \"onay\")'>Onayla</button> <button class='btn btn-danger' onclick='toggleStatus({$wait['id']}, \"red\")'>Gizle</button></td>";
                                        echo "</tr>";
                                        $wait_counter++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <br>

                    <div class="panel-heading">
                        Yayındaki Fotoğraflar
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive table-bordered">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Gönderilen Fotoğraf</th>
                                        <th>Açıklama</th>
                                        <th>Gönderen</th>
                                        <th>Gönderilme Tarihi</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $approved_counter = 1;
                                    while ($approved = mysqli_fetch_assoc($approved_result)) {
                                        echo "<tr>";
                                        echo "<td>{$approved_counter}</td>";
                                        echo "<td><img src='/uploads/users_upload/{$approved['image_name']}' style='width:100px;'></td>";
                                        echo "<td>{$approved['description']}</td>";
                                        echo "<td>{$wait['username']}</td>";
                                        echo "<td>{$approved['created_at']}</td>";
                                        echo "<td><button class='btn btn-danger' onclick='toggleStatus({$approved['id']}, \"ret\")'>Gizle</button></td>";
                                        echo "</tr>";
                                        $approved_counter++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <br>

                    <div class="panel-heading">
                        Onaylanmamış Fotoğraflar
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive table-bordered">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Gönderilen Fotoğraf</th>
                                        <th>Açıklama</th>
                                        <th>Gönderen</th>
                                        <th>Gönderilme Tarihi</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $deny_counter = 1;
                                    while ($deny = mysqli_fetch_assoc($deny_result)) {
                                        echo "<tr>";
                                        echo "<td>{$deny_counter}</td>";
                                        echo "<td><img src='/uploads/users_upload/{$deny['image_name']}' style='width:100px;'></td>";
                                        echo "<td>{$deny['description']}</td>";
                                        echo "<td>{$wait['username']}</td>";
                                        echo "<td>{$deny['created_at']}</td>";
                                        echo "<td><button class='btn btn-success' onclick='toggleStatus({$deny['id']}, \"onay\")'>Yayınla</button></td>";
                                        echo "</tr>";
                                        $deny_counter++;
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




