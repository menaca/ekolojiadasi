<?php

session_start();

require 'netting/connect.php';
require 'quiz/functions.php';

$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$quizzes = getAllQuizzes(); 

?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Quizler - Ekoloji Adası</title>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
  <style>
body{
       padding-left: 5%;
      padding-right: 5%;
  }
    .quiz-card {
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 20px;
      transition: transform 0.2s, box-shadow 0.2s;
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .quiz-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .quiz-card h5 {
      font-size: 1.25rem;
      font-weight: bold;
      margin-bottom: 10px;
    }

    .quiz-card p {
      font-size: 0.9rem;
      color: #666;
      margin-bottom: 0;
    }

    .quiz-card .icon {
      font-size: 24px;
      color: #3d3d3d;
    }
    .completed-info {
  margin-top: 10px;
}

.badge-success {
  background-color: #28a745;
  color: white;
  padding: 5px 10px;
  border-radius: 4px;
  font-size: 0.9rem;
}

.btn-primary {
  background-color: #007bff;
  border-color: #007bff;
  font-size: 0.9rem;
  padding: 5px 10px;
}

.btn-primary:hover {
  background-color: #0056b3;
  border-color: #004085;
}
  </style>
</head>
<body>

<?php include 'header.php';?>
<div class="d-flex justify-content-center">
<h2>Tüm Quizler</h2>
</div>
<div class="container mt-5">
  <div class="row">
    <?php foreach ($quizzes as $quiz): ?>
      <?php
      $quizId = $quiz['id'];
      $isCompleted = $userId ? hasUserCompletedQuiz($userId, $quizId) : null;
      ?>
      <div class="col-md-6 mb-4">
        <div class="quiz-card" data-href="/quiz/quiz.php?id=<?= $quizId ?>" onclick="window.location.href='/quiz/quiz.php?id=<?= $quizId ?>'">
          <div class="content">
        <?php if ($isCompleted): ?>
              <div class="completed-info">
                <span class="badge badge-success">Çözüldü</span>
                <a href="/quiz/result.php?id=<?= $quizId ?>">Sonuç Sayfasına Git</a>
                <br>
              </div>
            <?php endif; ?>
            <h5><?= htmlspecialchars($quiz['title']) ?></h5>
            <p><?= htmlspecialchars($quiz['description']) ?></p>
          </div>
          <div class="icon">
            <i class="fa fa-arrow-circle-o-right"></i>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php include 'footer.php';?>


  <script>
    document.querySelectorAll('.quiz-card').forEach(card => {
      card.addEventListener('click', () => {
        window.location.href = card.getAttribute('data-href');
      });
    });
  </script>
</body>
</html>