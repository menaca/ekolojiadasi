<?php

session_start();
require 'netting/connect.php';


$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$profile_id = isset($_GET['id']) ? $_GET['id'] : $user_id;

if (!$user_id && !$profile_id) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM users WHERE id = '$profile_id'";
$profile_result = mysqli_query($conn, $sql);
$profile_user = mysqli_fetch_assoc($profile_result);

if (!$profile_user) {
    header("Location: login.php");
    exit();
}


$threads_query = "SELECT * FROM forum_threads WHERE user_id = '$profile_id' ORDER BY created_at DESC";
$threads_result = mysqli_query($conn, $threads_query);

$comments_query = "SELECT fc.*, ft.title AS thread_title 
                   FROM forum_comments fc 
                   JOIN forum_threads ft ON fc.thread_id = ft.id 
                   WHERE fc.user_id = '$profile_id' 
                   ORDER BY fc.created_at DESC";
$comments_result = mysqli_query($conn, $comments_query);


$is_own_profile = ($profile_id == $user_id);

if($is_own_profile){
    $notification_query = "SELECT * FROM notifications WHERE user_id = '$profile_id' ORDER BY date DESC";
    $notification_result = mysqli_query($conn, $notification_query);
}

?>

<!DOCTYPE html>
<html lang="tr">    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" rel="stylesheet">

<head>
    <title>@<?php echo htmlspecialchars($profile_user['username']); ?> Profil Sayfası - Ekoloji Adası</title>
<style>
body{padding-top:20px;
background-color:#f1f5f9;
}
.card {
    border: 0;
    border-radius: 0.5rem;
    box-shadow: 0 2px 4px rgba(0,0,20,.08), 0 1px 2px rgba(0,0,20,.08);
}
.rounded-bottom {
    border-bottom-left-radius: 0.375rem !important;
    border-bottom-right-radius: 0.375rem !important;
}

.avatar-xxl {
    height: 7.5rem;
    width: 7.5rem;
}
.nav-lt-tab {
    border-top: 1px solid var(--dashui-border-color);
}
.px-4 {
    padding-left: 1rem!important;
    padding-right: 1rem!important;
}

.avatar-sm {
    height: 2rem;
    width: 2rem;
}

.nav-lt-tab .nav-item {
    margin: -0.0625rem 1rem 0;
}
.nav-lt-tab .nav-item .nav-link {
    border-radius: 0;
    border-top: 2px solid transparent;
    color: var(--dashui-gray-600);
    font-weight: 500;
    padding: 1rem 0;
}

.pt-20 {
    padding-top: 8rem!important;
}

.avatar-xxl.avatar-indicators:before {
    bottom: 5px;
    height: 16%;
    right: 17%;
    width: 16%;
}
.avatar-online:before {
    background-color: #198754;
}
.avatar-indicators:before {
    border: 2px solid #FFF;
    border-radius: 50%;
    bottom: 0;
    content: "";
    display: table;
    height: 30%;
    position: absolute;
    right: 5%;
    width: 30%;
}

.avatar-xxl {
    height: 7.5rem;
    width: 7.5rem;
}
.mt-n10 {
    margin-top: -3rem!important;
}
.me-2 {
    margin-right: 0.5rem!important;
}
.align-items-end {
    align-items: flex-end!important;
}
.rounded-circle {
    border-radius: 50%!important;
}
.border-2 {
    --dashui-border-width: 2px;
}
.border {
    border: 1px solid #dcdcdc !important;
}

.py-6 {
    padding-bottom: 1.5rem!important;
}

.bg-gray-300 {
    --dashui-bg-opacity: 1;
    background-color: #cbd5e1!important;
}

.mb-6 {
    margin-bottom: 1.5rem!important;
}
.align-items-center {
    align-items: center!important;
}


.mb-4 {
    margin-bottom: 1rem!important;
}

.mb-8 {
    margin-bottom: 2rem!important;
}
.shadow-none {
    box-shadow: none!important;
}

.card>.list-group:last-child {
    border-bottom-left-radius: 0.5rem;
    border-bottom-right-radius: 0.5rem;
    border-bottom-width: 0;
}
.card>.list-group:first-child {
    border-top-left-radius: 0.5rem;
    border-top-right-radius: 0.5rem;
    border-top-width: 0;
}
.card>.list-group {
    border-bottom: inherit;
    border-top: inherit;
}

.notification-item {
    display: flex;
    align-items: center;
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

.notification-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #ccc; 
    border-radius: 50%;
    margin-right: 15px; 
    font-size: 24px;
    background-color: #f8f9fa;
}

.notification-content {
    flex-grow: 1;
}

.notification-date {
    font-size: 12px;
    color: gray;
}

.tab-menu {
    display: flex;
    justify-content: start;
    border-bottom: 2px solid #ddd;
    margin-bottom: 20px;
}

.tab-item {
    padding: 10px 15px;
    cursor: pointer;
    font-weight: bold;
    color: #555;
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
}

.tab-item.active {
    color: #007bff;
    border-bottom: 3px solid #007bff;
}

</style>
</head>
<body class="u-body u-xl-mode" data-lang="tr">

    <?php include 'header.php'; ?>

    <div class="container">

<div class="row align-items-center">
  <div class="col-xl-12 col-lg-12 col-md-12 col-12">
    <div class="card rounded-bottom smooth-shadow-sm">
      <div class="d-flex align-items-center justify-content-between
        pt-5 pb-5 px-4">
        <div class="d-flex align-items-center">
          <div class="avatar-xxl avatar avatar me-2
            position-relative d-flex justify-content-end
            align-items-end">
            <img  style="object-fit: cover;" src="uploads/users_profile/<?php echo htmlspecialchars($profile_user['photo_url']); ?>" class="avatar-xxl
              rounded-circle border border-2" alt="Profil Fotoğrafı">
          </div>
          <div class="lh-1">
            <h2 class="mb-0"><?php echo htmlspecialchars($profile_user['real_name']); ?></h2>
            <p class="mb-0">@<?php echo htmlspecialchars($profile_user['username']); ?></p>
            <p class="mb-0"><?php echo htmlspecialchars($profile_user['point']); ?> Puan</p>
          </div>
        </div>
        <div>
        </div>
      </div>

    </div>
  </div>
</div>


<?php if($is_own_profile): ?>
<br>
<div class="tab-menu">
    <div id="forumTab" class="tab-item active">Forum</div>
    
    <div id="notificationTab" class="tab-item">Bildirimler</div>
    <div class="tab-item"><a style="color:#555;"href="change_photo.php">Profilinizi Düzenleyin</a></div>
</div>
<?php endif; ?>


<div id="forumList">
  <div class="row">
    <div class="col-lg-12 col-md-12 col-12">
      <div class="mb-12">
        <div style="background-color:#e7e7e7;"class="card rounded-bottom smooth-shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h5 class="mb-0">Oluşturduğu Konular</h5>
              </div>
            </div>
          </div>
        </div>
        <div class="card">
          <ul class="list-group list-group-flush">
          <?php if(mysqli_num_rows($threads_result) > 0): ?>
                                    <?php while($thread = mysqli_fetch_assoc($threads_result)): ?>
                                        <li class="list-group-item p-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="ms-3">
                                                        <p class="mb-0 font-weight-medium">
                                                            <a href="forum/thread.php?id=<?php echo $thread['id']; ?>">
                                                                <?php echo htmlspecialchars($thread['title']); ?>
                                                            </a>
                                                        </p>
                                                        <small class="text-muted"><?php echo $thread['extra_topic']; ?></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <li class="list-group-item p-3">Henüz forum mesajı oluşturulmamış.</li>
                                <?php endif; ?>
          </ul>
        </div>

        <br>

        <div class="mb-12">
                        <div style="background-color:#e7e7e7;" class="card rounded-bottom smooth-shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-0">Konulara bıraktığı mesajlar</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <ul class="list-group list-group-flush">
                                <?php if(mysqli_num_rows($comments_result) > 0): ?>
                                    <?php while($comment = mysqli_fetch_assoc($comments_result)): ?>
                                        <li class="list-group-item p-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="ms-3">
                                                        <p class="mb-0 font-weight-medium">
                                                            <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                                                        </p>
                                                        <small class="text-muted">
                                                        <a href="forum/thread.php?id=<?php echo $comment['thread_id']; ?>">
                                                                <?php echo htmlspecialchars($comment['thread_title']); ?>
                                                            </a>
                                                            <?php echo $comment['created_at']; ?> - 
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <li class="list-group-item p-3">Henüz etkinlik bulunamadı.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
      </div>

      <?php if ($is_own_profile): ?>
        <div class="row pt-5 pb-5 px-4 d-flex justify-content-center ">
            <a href="logout.php" class="btn btn-outline-danger 
            d-none d-md-block">Çıkış Yap</a>
            </div>    <?php endif; ?>
      </div>
     
    </div>
  </div>
</div>

<?php if($is_own_profile): ?>
<div id="notificationList" style="display: none;">
    <div class="mb-12">
        <div style="background-color:#e7e7e7;" class="card rounded-bottom smooth-shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Bildirimler</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
          <ul class="list-group list-group-flush">
    <?php while($notifications = mysqli_fetch_assoc($notification_result)): ?>
        <li class="list-group-item notification-item">
            <div class="notification-icon">
                <?php 
                if ($notifications['notification_type'] == 'like') {
                    echo '❤️';
                } elseif ($notifications['notification_type'] == 'comment') {
                    echo '💬'; // Yorum ikonu
                } elseif ($notifications['notification_type'] == 'follow') {
                    echo '👤'; // Takip ikonu
                } else {
                    echo '🔔'; // Genel bildirim ikonu
                }
                ?>
            </div>
            <div class="notification-content">
                <p class="mb-0 font-weight-medium">
                    <?php echo htmlspecialchars($notifications['notification_text']); ?>
                </p>
                <small class="notification-date">
                     <?php echo date("d M Y, H:i", strtotime($notifications['date'])); ?>
                </small>
            </div>
        </li>
    <?php endwhile; ?>
</ul>

        </div>
    </div>
</div>

<?php endif; ?>

</div>


</body>
    <?php include 'footer.php'; ?>
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('forumTab').addEventListener('click', function() {
    document.getElementById('forumList').style.display = 'block';
    document.getElementById('notificationList').style.display = 'none';

    document.getElementById('forumTab').classList.add('active');
    document.getElementById('notificationTab').classList.remove('active');
});

document.getElementById('notificationTab').addEventListener('click', function() {
    document.getElementById('forumList').style.display = 'none';
    document.getElementById('notificationList').style.display = 'block';

    document.getElementById('notificationTab').classList.add('active');
    document.getElementById('forumTab').classList.remove('active');
});

</script>

</html>
