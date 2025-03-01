<?php
session_start();

require 'netting/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

$alert_text = "";
$alert_type = "";

if (isset($_GET['status']) && $_GET['status'] == 'ok') {
  $alert_text = 'Fotoğraf Değerlendirilmeye Gönderildi!';
  $alert_type = 'success';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['description'];
    $user_id = $_SESSION['user_id'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageTmp = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $imageSize = $_FILES['image']['size'];
        $imageType = $_FILES['image']['type'];

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($imageType, $allowedTypes)) {
          $alert_text =  "Sadece JPEG, PNG veya GIF formatındaki resimler yüklenebilir.";
          $alert_type = 'danger';

            exit;
        }

        if ($imageSize > 20 * 1024 * 1024) {
          $alert_text =  "Resim dosyası 20MB'dan büyük olamaz.";
          $alert_type = 'danger';

            exit;
        }

        $imageInfo = getimagesize($imageTmp);
        if ($imageInfo === false) {
          $alert_text =  "Geçersiz resim dosyası.";
          $alert_type = 'danger';

            exit;
        }

        $newImageName = time() . '_' . uniqid() . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
        $targetDir = "uploads/users_upload/";
        $targetFile = $targetDir . basename($newImageName);

        if (move_uploaded_file($imageTmp, $targetFile)) {
            $query = "INSERT INTO images (user_id, image_name, description, status) VALUES ('$user_id','$newImageName', '$description', 'bekliyor')";
            mysqli_query($conn, $query);
            header('Location: upload_photo.php?status=ok'); 
            exit;
        } else {
          $alert_text = "Resim yüklenirken bir hata oluştu.";
          $alert_type = 'danger';
            exit;
        }
    } else {
      $alert_text = "Bir dosya seçmediniz.";
      $alert_type = 'danger';
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background-color: #014509;
        }
        
        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .text-white {
            color: #fff;
        }

        .image-area {
            border: 2px dashed rgba(255, 255, 255, 0.7);
            padding: 1rem;
            position: relative;
            border-radius: 8px;
            background-color: #f8f9fa;
        }

        .image-area::before {
            content: 'Uploaded image result';
            color: #fff;
            font-weight: bold;
            text-transform: uppercase;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 0.8rem;
            z-index: 1;
        }

        .image-area img {
            z-index: 2;
            position: relative;
            max-width: 100%;
            border-radius: 8px;
        }

        .input-group {
            border-radius: 50px;
        }

        .input-group .btn {
            border-radius: 50px;
        }

        .input-group input[type="file"] {
            opacity: 0;
        }

        textarea {
            width: 100%;
            height: 120px;
            border-radius: 10px;
            border: 1px solid #ced4da;
            padding: 10px;
            margin-top: 20px;
            font-size: 1rem;
        }

        .submit-btn {
            width: 100%;
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px;
            font-size: 1.1rem;
            font-weight: bold;
            border-radius: 10px;
            margin-top: 20px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .submit-btn:hover {
            background-color: #218838;
        }

        #upload-label {
            position: absolute;
            top: 50%;
            left: 1rem;
            transform: translateY(-50%);
        }

    </style>
</head>
<body>
<br>
<div class="container py-5">
    <div class="text-center">
        <h1 class="display-4 text-dark">Resimini Yükle</h1>
        <p class="lead mb-0">Bize resmini yolla ve bir açıklama bırak!</p>
    </div>

  
    <div class="row py-4">
        <div class="col-lg-6 mx-auto">
          
                 <?php if(strlen($alert_text) > 1): ?>
                      <div class="alert alert-<?php  echo  $alert_type?>" role="alert">
                            <?php  echo  $alert_text?>
                      </div>
                 <?php endif;?>   

            <form action="upload_photo.php" method="POST" enctype="multipart/form-data">
                <div class="image-area mt-4">
                    <img id="imageResult" src="#" alt="" class="img-fluid rounded shadow-sm mx-auto d-block">
                </div>
                <br>

                <div class="input-group mb-3 px-2 py-2 rounded-pill bg-white shadow-sm">
                    <input id="upload" name="image" type="file" accept=".jpg, .jpeg, .png" onchange="readURL(this);" class="form-control border-0" required>
                    <label id="upload-label" for="upload" class="font-weight-light text-muted">Dosya seçin</label>
                    <div class="input-group-append">
                        <label for="upload" class="btn btn-light m-0 rounded-pill px-4">
                            <i class="fa fa-cloud-upload mr-2 text-muted"></i>
                            <small class="text-uppercase font-weight-bold text-muted">Dosya Seç</small>
                        </label>
                    </div>
                </div>

                <textarea name="description" placeholder="Açıklama girin..." required></textarea>

                <button type="submit" class="submit-btn">Yükle</button>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#imageResult')
                    .attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    $(function () {
        $('#upload').on('change', function () {
            readURL(this);
        });
    });

    var input = document.getElementById('upload');
    var infoArea = document.getElementById('upload-label');

    input.addEventListener('change', showFileName);
    function showFileName(event) {
        var input = event.srcElement;
        var fileName = input.files[0].name;
        infoArea.textContent = 'Dosya adı: ' + fileName;
    }
</script>

</body>
</html>
