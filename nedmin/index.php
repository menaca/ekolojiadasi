<?php 

session_start(); 

require '../netting/connect.php';


$totalUsersCountResult = mysqli_query($conn, "SELECT COUNT(*) AS total_count FROM visitors");

if ($totalUsersCountResult) {
    $row = mysqli_fetch_assoc($totalUsersCountResult);
    $totalUsersCount = $row['total_count'];  
} else {
    echo "Sorgu hatası: " . mysqli_error($conn);
}

$usersCount = mysqli_query($conn,"SELECT COUNT(DISTINCT ip_address) AS unique_users_count FROM visitors");
if ($usersCount) {
    $row = mysqli_fetch_assoc($usersCount);
    $diffrentUsersCount = $row['unique_users_count'];  
} else {
    echo "Sorgu hatası: " . mysqli_error($conn);
}

$todayUsersCountResult = mysqli_query($conn, "SELECT COUNT(*) AS today_users_count FROM visitors WHERE DATE(time) = CURDATE()");
if ($todayUsersCountResult) {
    $row = mysqli_fetch_assoc($todayUsersCountResult);
    $todayUsersCount = $row['today_users_count']; 
} else {
    echo "Sorgu hatası: " . mysqli_error($conn);
}

$todayDiffrentUsersCountResult = mysqli_query($conn, "SELECT COUNT(DISTINCT ip_address) AS unique_today_users_count FROM visitors WHERE DATE(time) = CURDATE();");
if ($todayDiffrentUsersCountResult) {
    $row = mysqli_fetch_assoc($todayDiffrentUsersCountResult);
    $todayDiffrentUsersCount = $row['unique_today_users_count']; 
} else {
    echo "Sorgu hatası: " . mysqli_error($conn);
}

$query_forum_count = "SELECT COUNT(*) AS total_forums FROM forum_threads";
$result_forum_count = $conn->query($query_forum_count);
$total_forums = $result_forum_count->fetch_assoc()['total_forums'];

$approved_query_forum_count = "SELECT COUNT(*) AS approved_total_forums FROM forum_threads WHERE status='onay'";
$result_approved_forum_count = $conn->query($approved_query_forum_count);
$total_approved_forums = $result_approved_forum_count->fetch_assoc()['approved_total_forums'];

$query_comment_count = "SELECT COUNT(*) AS total_comments FROM forum_comments";
$result_comment_count = $conn->query($query_comment_count);
$total_comments = $result_comment_count->fetch_assoc()['total_comments'];

$approved_query_comment_count = "SELECT COUNT(*) AS approved_total_comments FROM forum_comments WHERE status='aktif'";
$approved_result_comment_count = $conn->query($approved_query_comment_count);
$approved_total_comments = $approved_result_comment_count->fetch_assoc()['approved_total_comments'];

?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<body>
    <div id="wrapper">
       

        <?php         include'widgets/header.php';         include 'widgets/sidebar.php';?>

        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-head-line">Anasayfa</h1>
                        <h1 class="page-subhead-line">Ekoloji Adasını Yönetebileceğiniz Yönetim Paneli.</h1>

                    </div>
                </div>
                <!-- /. ROW  -->
                <div class="row">
                    <div class="col-md-6">
                        <div style="background-color:#e7e7e7;" class="main-box">
                            <h2>Ziyaretçiler</h2>
                            <h1><?php echo $diffrentUsersCount; ?> / <?php echo $totalUsersCount; ?></h1>
                            <span class="day-details"><i class='bx bx-up-arrow-alt'></i>Toplam Farklı Ziyaretçiler / Toplam Ziyaretler</span>
                        </div>  
                      
                    </div>
                    <div class="col-md-6">
                        <div style="background-color:#e7e7e7;"class="main-box ">
                        <h2>Bugünkü Ziyaretçiler</h2>
            <h1><?php echo $todayDiffrentUsersCount; ?> / <?php echo $todayUsersCount; ?></h1>
            <span class="day-details"><i class='bx bx-up-arrow-alt'></i>Bugünkü farklı ziyaretçiler / Bugünkü toplam ziyaretler</span>
                        </div>
                    </div>                
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div style="background-color:#e7e7e7;" class="main-box">
                            <h2>Aktif Konular / Toplam  Konular</h2>
                            <h1><?php echo $total_approved_forums; ?> / <?php echo $total_forums; ?></h1>
                            <span class="day-details"><i class='bx bx-up-arrow-alt'></i>Yayındaki Konu Sayısı / Toplam Açılan Konu Sayısı</span>
                        </div>  
                      
                    </div>
                    <div class="col-md-6">
                        <div style="background-color:#e7e7e7;"class="main-box ">
                        <h2>Aktif Yanıtlar / Tüm Yanıtlar</h2>
            <h1><?php echo $approved_total_comments; ?> / <?php echo $total_comments; ?></h1>
            <span class="day-details"><i class='bx bx-up-arrow-alt'></i>Yayındaki Konu Mesajı Sayısı / Toplam Konu Mesajı Sayısı</span>
                        </div>
                    </div>                
                </div>
            </div>
        </div>
    </div>
    <?php include 'widgets/footer.php';?>
</body>
</html>
