<?php
session_start();

require '../netting/connect.php';

if ($_SESSION['admin_role'] != "admin") {
    header("Location: index.php?status=yetkisiz"); 
    exit();
}

$status = '';


if (isset($_GET['status']) && $_GET['status'] && $status == 'ok') {
    $status = 'Durum Değiştirildi!';
}

$query_waiting = "
    SELECT t.*, u.username 
    FROM forum_threads t 
    JOIN users u ON t.user_id = u.id 
    WHERE t.status = 'bekliyor' 
    ORDER BY t.id
";
$query_approved = "
    SELECT t.*, u.username 
    FROM forum_threads t 
    JOIN users u ON t.user_id = u.id 
    WHERE t.status = 'onay' 
    ORDER BY t.id
";
$query_deny = "
    SELECT t.*, u.username 
    FROM forum_threads t 
    JOIN users u ON t.user_id = u.id 
    WHERE t.status = 'ret' 
    ORDER BY t.id
";

$waiting_result = mysqli_query($conn, $query_waiting);
$approved_result = mysqli_query($conn, $query_approved);
$deny_result = mysqli_query($conn, $query_deny);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id']) && isset($_POST['status'])) {
        $threadId = $_POST['id'];
        $newStatus = $_POST['status'];

        if (in_array($newStatus, ['onay', 'ret', 'bekliyor'])) {
            $query = "UPDATE forum_threads SET status = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'si', $newStatus, $threadId);
            $result = mysqli_stmt_execute($stmt);

          if ($result && $newStatus == 'onay') {
                $query_user = "UPDATE users SET point = point + 25 WHERE id = (SELECT user_id FROM forum_threads WHERE id = ?)";
                $stmt_user = mysqli_prepare($conn, $query_user);
                mysqli_stmt_bind_param($stmt_user, 'i', $threadId);
                mysqli_stmt_execute($stmt_user);

                $notification_text = "Forum gönderiniz onaylandı. 25 puan kazandınız!";
                $notification_type = "point_add";
                $query_notification = "INSERT INTO notifications (user_id, notification_text, notification_type) VALUES ((SELECT user_id FROM forum_threads WHERE id = ?), ?, ?)";
                $stmt_notification = mysqli_prepare($conn, $query_notification);
                mysqli_stmt_bind_param($stmt_notification, 'iss', $threadId, $notification_text, $notification_type);
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

    <?php include 'widgets/header.php'; include 'widgets/sidebar.php';?>

        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-head-line">Ziyaretçilerden Gelen Forumları Yayınlayın</h1>
                        <h1 class="page-subhead-line">Ziyaretçilerden Gelen Forumları İnceleyin ve Onaylayın ya da Silin.</h1>
                    </div>
                </div>
                
                <?php if($status != null) echo "<div class='alert alert-success'>$status</div>"; ?>

                <div class="panel panel-default">
          
                    <div class="panel-heading">
                        Onay Bekleyen Forumlar
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive table-bordered">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Başlık</th>
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
                                        echo "<td>{$wait['title']}</td>";
                                        echo "<td>{$wait['username']}</td>";
                                        echo "<td>{$wait['created_at']}</td>";
                                        echo "<td>
                                                <button class='btn btn-success' onclick='toggleStatus({$wait['id']}, \"onay\")'>Onayla</button> 
                                                <button class='btn btn-danger' onclick='toggleStatus({$wait['id']}, \"ret\")'>Gizle</button>
                                              </td>";
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
                        Yayındaki Forumlar
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive table-bordered">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Başlık</th>
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
                                        echo "<td>{$approved['title']}</td>";
                                        echo "<td>{$approved['username']}</td>";
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
                        Reddedilmiş Forumlar
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive table-bordered">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Başlık</th>
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
                                        echo "<td>{$deny['title']}</td>";
                                        echo "<td>{$deny['username']}</td>";
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

    <?php include 'widgets/footer.php'; ?>
  

    <script>
    function toggleStatus(threadId, newStatus) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                location.reload(); // Sayfayı yenile
            }
        };
        xhr.send('id=' + threadId + '&status=' + newStatus); // Durumu gönder
    }
    </script>

</body>
</html>
