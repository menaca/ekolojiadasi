<?php 

session_start();
require 'netting/connect.php';

$blogResult = mysqli_query($conn,"SELECT * FROM blogs WHERE status='aktif' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html style="font-size: 16px;" lang="tr"><head><link rel="stylesheet" href="Blog.css" media="screen"></head>

<?php include 'header.php';?>

  <body data-path-to-root="./" data-include-products="false" class="u-body u-xl-mode" data-lang="tr">
    
    <section class="u-clearfix u-section-1" id="block-2">
      <div class="u-clearfix u-sheet u-valign-middle u-sheet-1">
        <h1 class="u-text u-text-default-lg u-text-default-md u-text-default-sm u-text-default-xl u-text-1">EKOBLOG</h1>
      </div>
    </section>
    <section class="u-clearfix u-section-2" id="block-1">
      <div class="u-clearfix u-sheet u-valign-middle u-sheet-1">
        <div class="u-blog u-expanded-width u-layout-grid u-blog-1" data-blog-id="blog-1">
          <div class="u-list-control"></div>
          <div class="u-repeater u-repeater-1">
          <?php
                    if ($blogResult->num_rows > 0) {
                        while ($row = mysqli_fetch_assoc($blogResult)) {
                            $id = $row['id'];
                            $title = $row['title'];
                            $excerpt = $row['excerpt']; 
                            $image = $row['photo'];
                            echo "
                                <div class='u-align-left u-blog-post u-container-style u-repeater-item u-white u-repeater-item-$id'>
                                    <div class='u-container-layout u-similar-container u-container-layout-$id'>
                                        <a class='u-post-header-link' href='blog_detail.php?id=$id'>
                                            <img style='height:350px' alt='' class='u-blog-control u-expanded-width u-image u-image-default u-image-$id' src='uploads/blogs/$image'>
                                        </a>
                                        <h4 class='u-blog-control u-text u-text-$id'>
                                            <a class='u-post-header-link' href='blog_detail.php?id=$id'>$title</a>
                                        </h4>
                                        <div class='u-blog-control u-post-content u-text u-text-2 fr-view'>$excerpt</div>
                                        <a href='blog_detail.php?id=$id' class='u-btn u-button-style u-none u-text-hover-palette-2-base u-text-palette-1-base u-btn-1'>
                                            Devamını oku
                                            <span class='u-icon u-text-palette-1-base'>
                                                <svg class='u-svg-content' viewBox='0 -32 426.66667 426' style='width: 1em; height: 1em;'>
                                                    <path d='m213.332031 181.667969c0 4.265625-1.277343 8.53125-3.625 11.730469l-106.667969 160c-3.839843 5.761718-10.238281 9.601562-17.707031 9.601562h-64c-11.730469 0-21.332031-9.601562-21.332031-21.332031 0-4.269531 1.28125-8.535157 3.625-11.734375l98.773438-148.265625-98.773438-148.269531c-2.34375-3.199219-3.625-7.464844-3.625-11.730469 0-11.734375 9.601562-21.335938 21.332031-21.335938h64c7.46875 0 13.867188 3.839844 17.707031 9.601563l106.667969 160c2.347657 3.199218 3.625 7.464844 3.625 11.734375zm0 0'></path>
                                                </svg>
                                            </span>
                                        </a>
                                    </div>
                                </div>";
                        }
                    } else {
                        echo "Daha hiç blog paylaşmadık!";
                    }
                    $conn->close();
                    ?>
          </div>
          <div class="u-list-control"></div>
        </div>
      </div>
    </section>
    
    
    
    <?php include 'footer.php';?>

</body></html>