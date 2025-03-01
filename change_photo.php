<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'netting/connect.php';

$user_id = $_SESSION['user_id'];

$sql = "SELECT photo_url FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

$current_photo = $user['photo_url'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['photo'])) {
    $target_dir = "uploads/users_profile/";  
    $imageFileType = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));

    $new_file_name = $user_id . '.' . $imageFileType;
    $target_file = $target_dir . $new_file_name;

    $uploadOk = 1;

    if (getimagesize($_FILES["photo"]["tmp_name"]) === false) {
        echo "Bu dosya bir resim değil.";
        $uploadOk = 0;
    }

    if ($_FILES["photo"]["size"] > 5000000) {
        echo "Dosya boyutu çok büyük.";
        $uploadOk = 0;
    }

    if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png" && $imageFileType != "gif") {
        echo "Sadece JPG, JPEG, PNG ve GIF dosyaları kabul ediliyor.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "Dosya yüklenemedi.";
    } else {
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $photo_url = $new_file_name; 

            $sql = "UPDATE users SET photo_url = '$photo_url' WHERE id = '$user_id'";
            if (mysqli_query($conn, $sql)) {
                echo "Profil fotoğrafınız başarıyla değiştirildi.";
                header("Location: profile.php");
                exit();
            } else {
                echo "Bir hata oluştu: " . mysqli_error($conn);
            }
        } else {
            echo "Fotoğraf yüklenirken bir hata oluştu.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Fotoğrafını Değiştir</title>
    <style>
        body {
            background-color: #f2f2f2;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 300px;
            text-align: center;
        }
        .form-container img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 20px;
        }
        .form-container input[type="file"] {
            margin-bottom: 20px;
        }
        .form-container input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .form-container input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Profil Fotoğrafını Değiştir</h2>

        <img id="profileImage" style="object-fit: cover;" src="uploads/users_profile/<?php echo htmlspecialchars($current_photo); ?>" alt="Mevcut Profil Fotoğrafı">

        <form action="change_photo.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="photo" accept="image/*" id="photoInput" onchange="previewImage(event)" required>
            <input type="submit" value="Yeni Fotoğrafı Yükle">
        </form>
    </div>

    <script>
        function previewImage(event) {
            var reader = new FileReader();

            reader.onload = function() {
                var output = document.getElementById('profileImage');
                output.src = reader.result; 
            }

            reader.readAsDataURL(event.target.files[0]); 
        }
    </script>
</body>
</html>
