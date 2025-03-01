
<?php

require 'netting/connect.php';

$sql = "SELECT i.image_name, i.description, u.username 
        FROM images i
        JOIN users u ON i.user_id = u.id
        WHERE i.status = 'onay' ORDER BY i.id DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html style="font-size: 16px;" lang="tr"><head><link href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="user_gallery.css" media="screen">

  <body data-path-to-root="./" data-include-products="false" class="u-body u-xl-mode" data-lang="tr">
 
  <?php include 'header.php'; ?>

    <section class="u-container-align-center u-section-1" >
      <div class="u-clearfix u-sheet u-sheet-1">
        <h2 class="u-align-center u-text u-text-default u-text-1"> Galeri</h2>
        <p class="u-align-center u-text u-text-grey-40 u-text-2"> Sizden gelen fotoğraf galerisi. Katkıda bulunan herkese sonsuz teşekkürler!&nbsp;</p>
        <div class="u-expanded-width u-gallery u-layout-grid u-no-transition u-lightbox u-show-text-on-hover u-gallery-1">
          <div class="container mt-5">
        <div class="row">
             <?php
                  $rank = 1;
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              echo '<div class="col-12 col-sm-6 col-md-4 col-lg-4 mb-4">';
              echo '<div class="card">';
              echo '<img class="card-img-top" src="uploads/users_upload/' . $row['image_name'] . '" alt="image">';
              echo '<div class="card-body">';
              echo '  <p class="card-text">' . $row['description'] . '</p>';
              echo '<p class="text-muted">' . $row['username'] . '</p>';
              echo '</div>';
              echo '</div>';
              echo '</div>';

              $rank++;
          }
      } else {
          echo 'No images found.';
      }

      $conn->close();
      ?>         
                      
                        
                    </div>
                </div>
            </div>
        </div>

    </section>
                <?php include 'footer.php'; ?>

    
    

    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>

  
</body></html>