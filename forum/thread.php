<?php 

session_start();
include '../netting/connect.php';


if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$thread_id = intval($_GET['id']);

$conn->query("UPDATE forum_threads SET view_count = view_count + 1 WHERE id = $thread_id");

$stmt = $conn->prepare("SELECT ft.*, u.username FROM forum_threads ft JOIN users u ON ft.user_id = u.id WHERE ft.id = ?");
$stmt->bind_param("i", $thread_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    die("Forum bulunamadı.");
}
$thread = $result->fetch_assoc();
if ($thread['status'] == 'ret') {
    header("Location: index.php");
    exit;
}
$stmt->close();

$stmt = $conn->prepare("SELECT fc.*, u.username FROM forum_comments fc JOIN users u ON fc.user_id = u.id WHERE fc.thread_id = ? ORDER BY fc.created_at ASC");
$stmt->bind_param("i", $thread_id);
$stmt->execute();
$replies = $stmt->get_result();
$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>EKOFORUM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
 body { 
    margin-top: 20px;
    color: #1a202c;
    text-align: left;
    background-color: #e2e8f0;    
}

.inner-wrapper {
    position: relative;
    height: calc(100vh - 3.5rem);
    transition: transform 0.3s;
}

@media (min-width: 992px) {
    .sticky-navbar .inner-wrapper {
        height: calc(100vh - 3.5rem - 48px); /* Sticky navbar için boyut ayarı */
    }
}

.inner-main,
.inner-sidebar {
    position: absolute;
    top: 0;
    bottom: 0;
    display: flex;
    flex-direction: column;
}

.inner-sidebar {
    left: 0;
    width: 235px;
    border-right: 1px solid #cbd5e0;
    background-color: #fff;
    z-index: 1;
}

.inner-main {
    right: 0;
    left: 235px;
}

.inner-main-footer,
.inner-main-header,
.inner-sidebar-footer,
.inner-sidebar-header {
    height: 3.5rem;
    border-bottom: 1px solid #cbd5e0;
    display: flex;
    align-items: center;
    padding: 0 1rem;
    flex-shrink: 0;
}

.inner-main-body,
.inner-sidebar-body {
    padding: 1rem;
    overflow-y: auto;
    position: relative;
    flex: 1 1 auto;
}

.inner-main-body .sticky-top,
.inner-sidebar-body .sticky-top {
    z-index: 999;
}

.inner-main-footer,
.inner-main-header {
    background-color: #fff;
}

.inner-main-footer,
.inner-sidebar-footer {
    border-top: 1px solid #cbd5e0;
    border-bottom: 0;
    height: auto;
    min-height: 3.5rem;
}

@media (max-width: 767.98px) {
    .inner-sidebar {
        left: -235px; /* Mobilde sidebar gizli */
    }
    
    .inner-main {
        left: 0;
    }
    
    .inner-expand .main-body {
        overflow: hidden;
    }
    
    .inner-expand .inner-wrapper {
        transform: translate3d(235px, 0, 0);
    }
}

.nav .show > .nav-link.nav-link-faded, 
.nav-link.nav-link-faded.active, 
.nav-link.nav-link-faded:active, 
.nav-pills .nav-link.nav-link-faded.active, 
.navbar-nav .show > .nav-link.nav-link-faded {
    color: #3367b5;
    background-color: #c9d8f0;
}

.nav-pills .nav-link.active, 
.nav-pills .show > .nav-link {
    color: #fff;
    background-color: #467bcb;
}

.nav-link.has-icon {
    display: flex;
    align-items: center;
}

.nav-link.active {
    color: #467bcb;
}

.nav-pills .nav-link {
    border-radius: .25rem;
}

.nav-link {
    color: #4a5568;
}

.card {
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, .1), 0 1px 2px 0 rgba(0, 0, 0, .06);
    position: relative;
    display: flex;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 0 solid rgba(0, 0, 0, .125);
    border-radius: .25rem;
}

.card-body {
    flex: 1 1 auto;
    min-height: 1px;
    padding: 1rem;
}

    </style>
</head>
<body>

<?php include '../header.php';?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/all.min.css" integrity="sha256-46r060N2LrChLLb5zowXQ72/iKKNiw/lAmygmHExk/o=" crossorigin="anonymous" />
<div class="container-fluid">
<div class="main-body p-0">
    <div class="inner-wrapper">
    <?php include 'widget/sidebar.php';?>
            <div class="inner-main-body p-2 p-sm-3 collapse forum-content show">
                <div class="container">
                        <h2 class="mt-4"><?php echo htmlspecialchars($thread['title']); ?></h>
                        <?php if(!empty($thread['extra_topic'])): ?>
                            <p class="text-secondary"><?php echo htmlspecialchars($thread['extra_topic']); ?></p>
                        <?php endif; ?>
                        <p class="text-muted">
                            Oluşturan: <a href="/profile.php?id=<?php echo $thread['user_id']; ?>"><?php echo $thread['username']; ?></a> | <?php echo $thread['created_at']; ?> | Görüntülenme: <?php echo $thread['view_count']; ?>
                        </p>
                        <hr>
                        <div class="mb-4">
                            <p><?php echo nl2br(htmlspecialchars($thread['content'])); ?></p>
                        </div>
                        
                        <div class="mb-4">
                            <h4>Yanıtlar</h4>
                            <?php if($replies->num_rows > 0): ?>
                            <?php while($reply = $replies->fetch_assoc()): ?>
                                <div class="card mb-2">
                                <div class="card-body">
                                    <p><?php echo nl2br(htmlspecialchars($reply['content'])); ?></p>
                                    <p class="text-muted small">
                                    Cevaplayan: <a href="/profile.php?id=<?php echo $reply['user_id']; ?>"><?php echo htmlspecialchars($reply['username']); ?></a> | <?php echo $reply['created_at']; ?>
                                    </p>
                                </div>
                                </div>
                            <?php endwhile; ?>
                            <?php else: ?>
                            <p>Henüz yanıt yok.</p>
                            <?php endif; ?>
                        </div>

                        <!-- Yanıt yazma formu -->
                       <div class="mb-4">
                            <h4>Yanıt Yaz</h4>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <form action="add_reply.php" method="post">
                                    <input type="hidden" name="thread_id" value="<?php echo $thread_id; ?>">
                                    <div class="form-group">
                                        <textarea name="content" class="form-control" rows="4" placeholder="Yanıtınızı yazınız..." required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Yanıt Gönder</button>
                                </form>
                            <?php else: ?>
                                <p>Yanıt yazmak için <a href="/login.php">giriş yapın</a>.</p>
                            <?php endif; ?>
                        </div>
                        <a href="index.php" class="btn btn-secondary">Geri Dön</a>
                        </div>
            </div>
        </div>
    </div>

</div>
</div>

<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript">
	
</script>

</body>
</html>