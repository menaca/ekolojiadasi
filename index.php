<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require 'netting/connect.php';


$hasBlog = false;
$sql = "SELECT * FROM blogs WHERE status='aktif' ORDER BY created_time DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $content = $row['excerpt'];
  $hasBlog = true;
}


$query_photos = "SELECT i.image_name, i.description, u.username 
        FROM images i
        JOIN users u ON i.user_id = u.id
        WHERE i.status = 'onay' ORDER BY i.id DESC";
$result_photos = $conn->query($query_photos);


$sql_users = "SELECT * FROM users WHERE status='aktif' ORDER BY point DESC LIMIT 5";
$result_users = $conn->query($sql_users);


$sql_quizzes = "SELECT id,title, description FROM quizzes ORDER BY id ASC LIMIT 2";
$result_quizzes = $conn->query($sql_quizzes);

$quizzes = [];
if ($result_quizzes->num_rows > 0) {
    while ($low = $result_quizzes->fetch_assoc()) {
        $quizzes[] = [
            'id' => $low['id'],
            'title' => $low['title'],
            'description' => $low['description']
        ];
    }
}
?>

<!DOCTYPE html>
<html style="font-size: 16px;" lang="tr"><head><link href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="index.css" media="screen">  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
</head>
    <body class="u-body u-xl-mode" data-lang="tr">
      
    <?php include 'header.php'; ?>
    
    <section class="u-clearfix u-section-1" id="carousel_71ee">
      <div class="u-clearfix u-sheet u-valign-middle-lg u-valign-middle-md u-valign-middle-sm u-valign-middle-xl u-sheet-1">
        <h1 class="u-custom-font u-text u-text-palette-3-base u-text-1">
          <font class="u-text-black"> Ekoloji adası </font>
        </h1>
        <img src="images/pexels-photo-962312.jpeg" alt="" class="u-expanded-width u-image u-image-default u-image-1" data-image-width="2254" data-image-height="1500">
      </div>
    </section>
    <section class="u-clearfix u-valign-middle-lg u-valign-middle-md u-valign-middle-sm u-valign-middle-xs u-section-2" id="sec-688e">
      <div class="u-clearfix u-sheet u-sheet-1">
        <h1 class="u-custom-font u-text u-text-palette-3-base u-text-1">
          <font class="u-text-black"> EKOFORUM'U ZİYARET ET</font>
        </h1>
        <div class="data-layout-selected u-clearfix u-expanded-width u-gutter-0 u-layout-wrap u-radius u-layout-wrap-1">
          <div class="u-layout">
            <div class="u-layout-row">
              <div class="u-container-style u-custom-color-4 u-layout-cell u-radius u-shape-round u-size-60 u-layout-cell-1">
                <div class="u-container-layout u-container-layout-1">
                  <h3 class="u-text u-text-2"> İklim ve doğa hakkında soruların mı var? Hemen bir tartışma başlat!</h3>
                  <a href="forum/index.php" class="u-align-center u-border-none u-btn u-btn-round u-button-style u-custom-font u-heading-font u-radius u-text-grey-70 u-white u-btn-1">EKOFORUM</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

     <section class="u-clearfix u-valign-middle-lg u-valign-middle-md u-valign-middle-sm u-valign-middle-xs u-section-2" id="sec-688e">
      <div class="u-clearfix u-sheet u-sheet-1">
        <h1 class="u-custom-font u-text u-text-palette-3-base u-text-1">
          <font class="u-text-black">EKOQUİZ'LERE GÖZ AT!</font>
        </h1>
        <p class="u-align-left u-custom-font u-heading-font u-text u-text-2">Quizleri çöz, insanlarla yarış puanlar kazan!</p>
        
        
        <div class="container mt-5">
    <div class="row">
       <?php foreach ($quizzes as $index => $quiz): ?>
                    <!-- Quiz card -->
                    <div data-href="quiz/quiz.php?id=<?php echo $quiz['id']; ?>" class="col-md-6 mb-4">
                        <div class="quiz-card">
                            <div class="content">
                                <h5><?= htmlspecialchars($quiz['title']) ?></h5>
                                <p><?= htmlspecialchars($quiz['description']) ?></p>
                            </div>
                            <div class="icon">
                                <i style="font-size: 24px;color: #3d3d3d;" class="fa fa-arrow-circle-o-right"></i>
                            </div>
                        </div>
                    </div>
                    </a>
                <?php endforeach; ?>
    </div>
</div>

<div class="data-layout-selected u-clearfix u-expanded-width u-gutter-0 u-layout-wrap u-radius u-layout-wrap-1">
          <div class="u-layout">
            <div class="u-layout-row">
              <div class="u-container-style u-custom-color-4 u-layout-cell u-radius u-shape-round u-size-60 u-layout-cell-1">
                <div class="u-container-layout u-container-layout-1">
                  <h3 class="u-text u-text-2"> Daha fazla soru çözmeye ne dersin? <br> Hadi quizlere göz at!</h3>
                  <a href="quizzes.php" class="u-align-center u-border-none u-btn u-btn-round u-button-style u-custom-font u-heading-font u-radius u-text-grey-70 u-white u-btn-1">EKOQUİZ</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    
    <?php if ($hasBlog): ?>
    <section class="u-clearfix u-valign-middle-lg u-valign-middle-md u-valign-middle-sm u-valign-middle-xs u-section-3" id="sec-b306">
      <div class="u-clearfix u-sheet u-sheet-1">
        <h1 class="u-custom-font u-text u-text-palette-3-base u-text-1">
          <font class="u-text-black"> SON YAZIMIZI OKUDUN MU?</font>
        </h1>
        <div class="data-layout-selected u-clearfix u-expanded-width u-layout-wrap u-layout-wrap-1">
          <div class="u-layout">
            <div class="u-layout-col">
              <div  class="u-container-style u-image u-layout-cell u-size-30 u-image-1" data-image-width="1500" data-image-height="1070">
                <div class="u-container-layout u-valign-bottom u-container-layout-1"></div>
              </div>
              <div class="u-align-left u-container-align-left-lg u-container-align-left-xl u-container-style u-layout-cell u-shape-rectangle u-size-30 u-layout-cell-2">
                <div class="u-container-layout u-container-layout-2">
                  <h2 class="u-text u-text-2"><?php echo htmlspecialchars($row['title']);?></h2>
                  <div class="u-border-3 u-border-black u-line u-line-horizontal u-line-1"></div>
                  <h6 class="u-text u-text-custom-color-2 u-text-3"><?php echo htmlspecialchars($row['subject']);?></h6>
                  <p class="u-text u-text-grey-40 u-text-4"><?php echo htmlspecialchars($content);?></p>
                  <a href="blog_detail.php?id=<?php echo $row['id'];?>" class="u-btn u-button-style u-none u-text-hover-palette-2-base u-text-palette-1-base u-btn-1">Devamını oku&nbsp;<span class="u-icon u-text-palette-1-base"><svg class="u-svg-content" viewBox="0 -32 426.66667 426" style="width: 1em; height: 1em;"><path d="m213.332031 181.667969c0 4.265625-1.277343 8.53125-3.625 11.730469l-106.667969 160c-3.839843 5.761718-10.238281 9.601562-17.707031 9.601562h-64c-11.730469 0-21.332031-9.601562-21.332031-21.332031 0-4.269531 1.28125-8.535157 3.625-11.734375l98.773438-148.265625-98.773438-148.269531c-2.34375-3.199219-3.625-7.464844-3.625-11.730469 0-11.734375 9.601562-21.335938 21.332031-21.335938h64c7.46875 0 13.867188 3.839844 17.707031 9.601563l106.667969 160c2.347657 3.199218 3.625 7.464844 3.625 11.734375zm0 0"></path><path d="m426.667969 181.667969c0 4.265625-1.28125 8.53125-3.628907 11.730469l-106.664062 160c-3.839844 5.761718-10.242188 9.601562-17.707031 9.601562h-64c-11.734375 0-21.335938-9.601562-21.335938-21.332031 0-4.269531 1.28125-8.535157 3.628907-11.734375l98.773437-148.265625-98.773437-148.269531c-2.347657-3.199219-3.628907-7.464844-3.628907-11.730469 0-11.734375 9.601563-21.335938 21.335938-21.335938h64c7.464843 0 13.867187 3.839844 17.707031 9.601563l106.664062 160c2.347657 3.199218 3.628907 7.464844 3.628907 11.734375zm0 0"></path></svg></span>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <?php endif;?>
    <section class="u-clearfix u-valign-middle-lg u-valign-middle-md u-valign-middle-sm u-valign-middle-xs u-section-4" id="sec-e0b2">
      <div class="u-clearfix u-sheet u-sheet-1">
        <h1 class="u-custom-font u-text u-text-grey-50 u-text-1">
          <font class="u-text-black"> EKOBLO​G'U ZİYARET ​ET</font>
        </h1>
        <div class="data-layout-selected u-clearfix u-expanded-width u-gutter-0 u-layout-wrap u-radius u-layout-wrap-1">
          <div class="u-layout">
            <div class="u-layout-row">
              <div class="u-container-style u-custom-color-4 u-layout-cell u-radius u-shape-round u-size-60 u-layout-cell-1">
                <div class="u-container-layout u-container-layout-1">
                  <h3 class="u-text u-text-2"> İklim ve doğa hakkında daha çok şeyler öğrenmeye hazır mısın?&nbsp;</h3>
                  <a href="blogs.php" class="u-align-center u-border-none u-btn u-btn-round u-button-style u-custom-font u-heading-font u-radius u-text-grey-70 u-white u-btn-1">EKOBLOG </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section class="u-clearfix u-valign-middle-lg u-valign-middle-md u-valign-middle-sm u-valign-middle-xs u-section-5" id="sec-21fd">
      <div class="u-clearfix u-sheet u-sheet-1">
        <h1 class="u-custom-font u-text u-text-palette-3-base u-text-1">
          <font class="u-text-black"> SİZDEN GELENLER</font>
        </h1>
        <p class="u-align-left u-custom-font u-heading-font u-text u-text-2">Sizden gelen fotoğraflar. ​Hadi sen de bir fotoğraf yükle galerimize katkın olsun!</p>
        <div class="data-layout-selected u-clearfix u-expanded-width u-gutter-0 u-layout-wrap u-radius u-layout-wrap-1">
          <div class="u-layout">
            <div class="u-layout-row">
              <div class="u-container-style u-grey-5 u-layout-cell u-radius u-shading u-shape-round u-size-60 u-layout-cell-1">
                <div class="u-container-layout u-container-layout-1">
                  <a href="upload_photo.php" class="u-align-center u-border-none u-btn u-btn-round u-button-style u-custom-font u-heading-font u-radius u-text-grey-70 u-white u-btn-1">Fotoğraf Yükle</a>
                  <h3 class="u-text u-text-3"> Hadi sen de fotoğraf yükle!</h3>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="u-expanded-width u-gallery u-layout-grid ">
          <div class="u-gallery-inner u-gallery-inner-1">
           <div class="row">
             <?php
                   if ($result_photos->num_rows > 0) {
          while ($user_photo = $result_photos->fetch_assoc()) {
              echo '<div class="col-12 col-sm-6 col-md-4 col-lg-4 mb-4">';
              echo '<div class="card">';
              echo '<img class="card-img-top" src="uploads/users_upload/' . $user_photo['image_name'] . '" alt="image">';
              echo '<div class="card-body">';
              echo '  <p class="card-text">' . htmlspecialchars($user_photo['description']) . '</p>';
              echo '<p class="text-muted">' . htmlspecialchars($user_photo['username']) . '</p>';
              echo '</div>';
              echo '</div>';
              echo '</div>';
          }
      } else {
          echo 'No images found.';
      }

      $conn->close();
      ?>         
                      
                        
                </div>
          </div>
        </div>
        <div class="data-layout-selected u-clearfix u-expanded-width u-gutter-0 u-layout-wrap u-radius u-layout-wrap-2">
          <div class="u-layout">
            <div class="u-layout-row">
              <div class="u-container-style u-custom-color-4 u-layout-cell u-radius u-shading u-shape-round u-size-60 u-layout-cell-2">
                <div class="u-container-layout u-container-layout-2">
                  <a href="user_gallery.php" class="u-align-center u-border-none u-btn u-btn-round u-button-style u-custom-font u-heading-font u-radius u-text-grey-70 u-white u-btn-2">Fotoğraf Galerisi </a>
                  <h3 class="u-text u-text-4"> Tüm sizden gelenler galerimize göz at!</h3>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section class="u-clearfix u-section-6" id="ranks">
      <div class="u-clearfix u-sheet u-sheet-1">
        <h1 class="u-custom-font u-text u-text-palette-3-base u-text-1">
          <font color="#000000">EKO-SIRALAMASI</font>
        </h1>
        <h1 class="u-custom-font u-text u-text-palette-3-base u-text-2">
          <span style="color: rgb(0, 0, 0);">Sıralamada üste çıkmak için bloglarımızı oku, oyunları tamamla forumda tartış!</span>
        </h1>
        <div class="u-expanded-width-xs u-list u-list-1">
          <div class="u-repeater u-repeater-1">
            <?php
                $rank = 1;
                while ($ranked_user = $result_users->fetch_assoc()) {
                ?>
                    <div class="u-container-style u-list-item u-repeater-item">
                        <div class="u-container-layout u-similar-container u-container-layout-<?php echo $rank; ?>">
                            <img class="u-image u-image-circle u-image-1" src="/uploads/users_profile/<?php echo $ranked_user['photo_url']; ?>" alt="" data-image-width="853" data-image-height="1280">
                            <h5 class="u-text u-text-grey-40 u-text-3"><?php echo $rank; ?>.</h5>
                            <a href="profile.php?id=<?php echo $ranked_user['id']; ?>"><h5 class="u-text u-text-4"><?php echo htmlspecialchars($ranked_user['username']); ?></h5></a>
                            <h5 class="u-text u-text-5"><?php echo htmlspecialchars($ranked_user['real_name']); ?></h5>
                            <h5 class="u-text u-text-5"><?php echo $ranked_user['point']; ?> Puan</h5>
                        </div>
                    </div>
                <?php
                    $rank++;
                }
                ?>
            
            
          </div>
        </div>
      </div>
    </section>
</body>

<?php include 'footer.php'; ?>

<style>
.quiz-card {
            border: 3px solid #ccc;
            background-color: tranparent;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .quiz-card .content {
            text-align: center;
            flex: 1;
        }
        .quiz-card .content h5 {
            margin-bottom: 10px;
        }
        .quiz-card .content p {
            font-size: 0.9rem;
            color: #555;
        }
        .quiz-card:hover {
            border: 3px solid green;
        }

        @media (max-width: 767px) {
            .quiz-card {
                flex-direction: column;
                text-align: center;
            }
            .quiz-card .content {
                margin-bottom: 10px;
            }
            .quiz-card .icon {
                margin-top: 10px;
            }
        }
  .u-section-3 .u-image-1 {
  background-image: url("uploads/blogs/<?php echo $row['photo'];?>");
  background-position: 50% 50%;
  min-height: 411px;
}
</style>
</html>