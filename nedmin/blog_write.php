<?php

session_start(); 

require '../netting/connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $subject = $_POST['subject'];
    $excerpt = $_POST['excerpt'];
    $writerId = $_SESSION['admin_id'];   

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $uploadDir = '../uploads/blogs/';
        $uploadFile = $uploadDir . basename($_FILES['photo']['name']);
        $fileName = basename($_FILES['photo']['name']); 

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif']; 
        if (in_array($_FILES['photo']['type'], $allowedTypes)) {
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
                $photoPath = $fileName;
            } else {
                echo "Dosya yükleme hatası!";
                exit;
            }
        } else {
            echo "Geçersiz dosya tipi!";
            exit;
        }
    } else {
        echo "Fotoğraf yüklenmedi!";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO blogs (title, content, photo, subject, excerpt,writer_id) VALUES (?, ?, ?, ?, ?,?)");
    $stmt->bind_param("ssssss", $title, $content, $photoPath, $subject, $excerpt, $writerId);

    if ($stmt->execute()) {
        header("Location: blog_list.php?status=ok");
    } else {
        echo "Veritabanına kaydetme hatası: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<script src="https://cdn.tiny.cloud/1/3cqnnbs0ffkw2lgem8echcc9pf3hedtjripv5rd5ws1v662w/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

<body>
    <div id="wrapper">

        <?php include 'widgets/header.php';
        include 'widgets/sidebar.php'; ?>


        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-head-line">YENİ BLOG YAYINLAYIN</h1>
                        <h1 class="page-subhead-line">Yeni Blog Yazınızı Yayınlıyorsunuz. </h1>
                    </div>
                </div>

                <div class="col-md-6">
                    <form action="blog_write.php" method="POST" enctype="multipart/form-data">
                        <label for="photo">Başlık Fotoğrafı:</label>
                        <input class="form-control" type="file" id="photo" name="photo" accept="image/*" required onchange="previewImage();">

                        <label for="title">Başlık:</label>
                        <input class="form-control" type="text" id="title" name="title" required oninput="updatePreview();">

                        <label for="subject">AltBaşlık:</label>
                        <input class="form-control" type="text" id="subject" name="subject" required oninput="updatePreview();">

                        <label for="content">İçerik:</label>
                        <textarea class="form-control" id="content" name="content" ></textarea>

                        <label for="excerpt">Sergilenecek Özet:</label>
                        <textarea class="form-control" id="excerpt" name="excerpt" oninput="updatePreview();"></textarea>

                        <br>

                        <input type="submit" class="btn btn-info" value="Kaydet"></input>
                        
                        <br><br>
                    </form>
                </div>

                <div class="col-md-6" id="preview-container">
                    <div id="image-preview" style="width: 100%; height: auto; margin-bottom: 20px;">
                        <img id="preview-image" src="" alt="Resim önizlemesi" style="width: 100%; max-height: 300px; object-fit: cover;">
                    </div>
                    <div id="preview-title" style="font-size: 24px; font-weight: bold;"></div>
                    <div id="preview-subject" style="font-size: 20px; color: gray; margin-top: 10px;"></div>
                    <div id="preview-excerpt" style="margin-top: 20px; color: #333;"></div>
                </div>
            </div>
        </div>

    </div>

    <?php include 'widgets/footer.php'; ?>

    <script>
        tinymce.init({
            selector: '#content', 
            height: 300, 
            menubar: false,
            plugins: 'link image code',
            toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | link image | code', 
        });

        function previewImage() {
            const file = document.getElementById('photo').files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                document.getElementById('preview-image').src = e.target.result;
            }

            if (file) {
                reader.readAsDataURL(file);
            }
        }

        // Başlık, alt başlık ve içerik önizlemesi
        function updatePreview() {
            const title = document.getElementById('title').value;
            const subject = document.getElementById('subject').value;
            const excerpt = document.getElementById('excerpt').value;

            document.getElementById('preview-title').textContent = title;
            document.getElementById('preview-subject').textContent = subject;
            document.getElementById('preview-excerpt').innerHTML = excerpt;
        }
    </script>
</body>
</html>