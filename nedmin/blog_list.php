<?php

session_start(); 

require '../netting/connect.php';

$status = '';

if (isset($_GET['status']) && $_GET['status'] == 'ok') {
    $status = 'Blog eklendi!';
}

$query_blog = "SELECT * FROM blogs ORDER BY id";

$blogs_result = mysqli_query($conn, $query_blog);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id']) && isset($_POST['status'])) {
        $blogId = $_POST['id'];
        $newStatus = $_POST['status'];

        $query = "UPDATE blogs SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'si', $newStatus, $blogId);
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
                        <h1 class="page-head-line">Bloglarınız</h1>
                        <h1 class="page-subhead-line">Paylaştığınız Tüm Blogları Görüntüleyin Düzenleyin </h1>
                    </div>
                </div>
                
                <?php if($status != null) echo "<div class='alert alert-success'>$status</div>"; ?>

                <a href="blog_write.php">
                <button  class='btn btn-warning'>Blog Yazısı Ekle</button>
                </a>

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
                                        <th>Başlık</th>
                                        <th>Konu</th>
                                        <th>Sergilenecek Özet</th>
                                        <th>Yazılma Tarihi</th>
                                        <?php if($_SESSION['admin_role']  == 'admin'):?>
                                        <th>İşlem</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $blog_counter = 1;
                                    while ($blog = mysqli_fetch_assoc($blogs_result)) {

                                        $status_button = ($blog['status'] == 'aktif') 
                                        ? "<button class='btn btn-danger' onclick='toggleStatus({$blog['id']}, \"pasif\")'>Deaktive Et</button>"
                                        : "<button class='btn btn-success' onclick='toggleStatus({$blog['id']}, \"aktif\")'>Aktive Et</button>";


                                        echo "<tr>";
                                        echo "<td>{$blog_counter}</td>";
                                        echo "<td><img src='/uploads/blogs/{$blog['photo']}' alt='Profil Fotoğrafı' style='width:50px; height:50px;'></td>";
                                        echo "<td>{$blog['title']}</td>";
                                        echo "<td>{$blog['subject']}</td>";
                                        echo "<td>{$blog['excerpt']}</td>";
                                        echo "<td>{$blog['created_time']}</td>";
                                        if($_SESSION['admin_role']  == 'admin'):
                                        echo "<td>$status_button</td>";
                                        endif;
                                        echo "</tr>";
                                        $blog_counter++;
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




