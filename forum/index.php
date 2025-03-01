<?php 

session_start();
include '../netting/connect.php';

$alert_text = '';

if (isset($_GET['status']) && $_GET['status'] == 'ok') {
    $alert_text = 'Konunuz değerlendirilmeye gönderildi! En yakın zamanda değerlendirilecek!';
}


$query = "
SELECT 
    t.*,
    u.username AS creator_username,
    u.photo_url AS creator_photo,
    (SELECT COUNT(*) FROM forum_comments fc WHERE fc.thread_id = t.id) AS comment_count,
    (SELECT u2.username FROM forum_comments fc 
         JOIN users u2 ON fc.user_id = u2.id 
         WHERE fc.thread_id = t.id 
         ORDER BY fc.created_at DESC LIMIT 1) AS last_reply_username,
    (SELECT u2.photo_url FROM forum_comments fc 
         JOIN users u2 ON fc.user_id = u2.id 
         WHERE fc.thread_id = t.id 
         ORDER BY fc.created_at DESC LIMIT 1) AS last_reply_photo
FROM forum_threads t
JOIN users u ON t.user_id = u.id
WHERE t.status = 'onay'
ORDER BY t.created_at DESC
";


$result = $conn->query($query);
$threads = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $threads[] = $row;
    }
}


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
            max-width: 100%; 
        }

        @media (min-width: 992px) {
            .sticky-navbar .inner-wrapper {
                height: calc(100vh - 3.5rem - 48px);
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

@media (max-width: 767px) {
    .toggle-sidebar-btn {
        display: block; 
    }

    .inner-sidebar {
        display: none; 
    }

                .inner-main {
                left: 0; 
            }

            .inner-main-body {
                padding-top: 1rem;
            }

            .card {
                margin-bottom: 1rem;
            }

            .media img {
                width: 35px; /* Fotoğraf genişliği küçültülür */
                height: 35px;
            }

            h6 {
                font-size: 1rem;
            }

            .media-body p {
                font-size: 0.9rem;
            }

            .card-body .text-center {
                font-size: 0.8rem;
            }
}

@media (min-width: 768px) {
    .inner-sidebar {
        display: block; 
    }

    .toggle-sidebar-btn {
        display: none; 
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
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;
        }
        .card-body .media-body p {
    word-wrap: break-word;  
    overflow-wrap: break-word; 
    word-break: break-word;
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

            <?php if(strlen($alert_text) > 1): ?>
                      <div class="alert alert-success" role="alert">
                            <?php  echo  $alert_text?>
                      </div>
                  <?php endif;?>    

               <?php if(!empty($threads)): ?>
                        <?php foreach ($threads as $thread): ?>
                        <div class="card mb-2">
                            <div class="card-body">
                            <div class="media">
                            <a href="thread.php?id=<?php echo $thread['id']; ?>">
              <img style="object-fit: cover;" src="/uploads/users_profile/<?php echo htmlspecialchars($thread['creator_photo']); ?>" class="mr-3 rounded-circle" width="50" height="50" alt="<?php echo htmlspecialchars($thread['creator_username']); ?>">
            </a>
                                <div class="media-body">
                                <h6>
                                    <a href="thread.php?id=<?php echo $thread['id']; ?>" class="text-body">
                                    <?php echo htmlspecialchars($thread['title']); ?>
                                    </a>
                                </h6>
                                <?php if(!empty($thread['extra_topic'])): ?>
                                    <p class="text-secondary"><?php echo htmlspecialchars($thread['extra_topic']); ?></p>
                                <?php endif; ?>
                                <p class="text-muted">
                                    <a href="/profile.php?id=<?php echo $thread['user_id']; ?>">Oluşturan <span style="color:grey"><?php echo $thread['creator_username']; ?></span></a>
                                    <?php if($thread['last_reply_username']): ?>
                                    | En son cevap veren: <strong><?php echo htmlspecialchars($thread['last_reply_username']); ?></strong>
                                    <?php else: ?>
                                    | Henüz cevap yok.
                                    <?php endif; ?>
                                </p>
                                </div>
                                <div class="text-muted text-center">
                                <span class="d-block"><i class="far fa-eye"></i> <?php echo $thread['view_count']; ?></span>
                                <span class="d-block"><i class="far fa-comment"></i> <?php echo $thread['comment_count']; ?></span>
                                </div>
                            </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Henüz forum başlığı oluşturulmamış.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</div>

<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>