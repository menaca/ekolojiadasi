<?php

session_start();
require 'netting/connect.php';

if (isset($_GET['id'])) {
    $blogId = $_GET['id'];
    $userId = $_SESSION['user_id'] ?? null; 


    $stmt = $conn->prepare("SELECT * FROM blogs WHERE id = ?");
    $stmt->bind_param("i", $blogId); 

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if ($row['status'] == 'pasif') {
          header("Location: blogs.php");
          exit;
      }

        $title = $row['title'];
        $subject = $row['subject'];
        $content = $row['content'];
        $photo = $row['photo'];
        $writerId = (int)$row['writer_id'];  

    } else {
        echo "Blog yazısı bulunamadı.";
        exit;
    }

    $stmt->close();

    $stmt = $conn->prepare("SELECT name,username,role,profile_picture FROM admins WHERE id = ?");
    $stmt->bind_param("i",  $writerId); 
    $stmt->execute();
    $adminResult = $stmt->get_result();

    if ($adminResult->num_rows > 0) {
        $writer = $adminResult->fetch_assoc();
        $authorName = $writer['name']; 
        $authorUserName = $writer['username']; 
        $authorRole = $writer['role']; 
        $authorPhoto = $writer['profile_picture'];  
    } else {
        $authorName = "Anonim";
        $authorUserName = "";  
        $authorRole = "Yazar";
        $authorPhoto = "default_pp.jpg";
    }

    $stmt->close();

    if ($userId) {
            $stmt = $conn->prepare("SELECT * FROM user_blog_views WHERE user_id = ? AND blog_id = ?");
            $stmt->bind_param("ii", $userId, $blogId);
            $stmt->execute();
            $viewResult = $stmt->get_result();

            if ($viewResult->num_rows == 0) {
                $stmt = $conn->prepare("UPDATE users SET point = point + 20 WHERE id = ?");
                $stmt->bind_param("i", $userId);
                $stmt->execute();

                $notificationText = "{$title} başlıklı blog yazısını okudunuz. 20 puan kazandınız!";            
                $notificationType = "point_add";
                $stmt = $conn->prepare("INSERT INTO notifications (user_id, notification_text, notification_type) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $userId, $notificationText, $notificationType);
                $stmt->execute();

                $stmt = $conn->prepare("INSERT INTO user_blog_views (user_id, blog_id) VALUES (?, ?)");
                $stmt->bind_param("ii", $userId, $blogId);
                $stmt->execute();
            } else {
                $alreadyRead = true;
            }

            $stmt->close();
}
    } else {
        echo "Blog ID'si eksik!";
        exit;
    }

$conn->close();
?>


<!DOCTYPE html>
<html style="font-size: 16px;" lang="tr"><head><link rel="stylesheet" href="Sayfa-2.css" media="screen"></head>

<?php include 'header.php';?>

  <body data-path-to-root="./" data-include-products="false" class="u-body u-xl-mode" data-lang="tr">
    <section class="u-clearfix u-section-1" id="sec-50e3">
      <div class="u-clearfix u-sheet u-valign-bottom u-sheet-1">

            <?php if (isset($alreadyRead) && $alreadyRead): ?>
                <span class="badge badge-success">Okundu</span>
            <?php endif; ?>    
            
        <h5 class="u-text u-text-default u-text-1"><?php echo $subject ?></h5>
        <h2 class="u-text u-text-default u-text-2"><?php echo $title ?></h2>
        <img class="u-expanded-width u-image u-image-default u-image-1" src="uploads/blogs/<?php echo $photo ?>" alt="" data-image-width="425" data-image-height="283">
      </div>
    </section>
    <section class="u-clearfix u-white u-section-2" id="sec-3826">
      <div class="u-clearfix u-sheet u-sheet-1">
        <p class="u-text u-text-default u-text-1"><?php echo $content ?>
        </p>
      </div>
    </section>
    <section class="u-clearfix u-section-3" id="block-1">
      <div class="u-clearfix u-sheet u-sheet-1">
        <img class="u-image u-image-circle u-image-1" src="uploads/users_profile/<?php echo $authorPhoto?>" alt="" data-image-width="853" data-image-height="1280">
        <h2 class="u-text u-text-default-lg u-text-default-xl u-text-1"><?php echo $authorName?></h2>
        <h2 class="u-text u-text-default-lg u-text-default-xl u-text-2">@<?php echo $authorUserName;?> <?php echo $authorRole?> </h2>
      </div>
    </section>
    
    
    <style>
        .badge-success {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.9rem;
            width:75px;
            }
    </style>
    
    <?php include 'footer.php';?>

  
</body></html>