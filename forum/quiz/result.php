<?php

require '../netting/connect.php';
require 'functions.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}


if (isset($_GET['id'])) {
    $userId = $_SESSION['user_id'];  
    $quizId = $_GET['id'];

    $questions = getQuizQuestions($quizId);
    $quizName = getQuizName($quizId);

    $results = calculateQuizResults($userId, $quizId);
    $correctCount = $results['correct_count'];
    $incorrectCount = $results['incorrect_count'];
    $emptyCount = $results['empty_count'];

    $userScore = $correctCount * 20;
    $totalScore = count($questions) * 20;

    $topUsers = getTopUsers($quizId);
} else {
    echo "Quiz ID'si eksik!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= htmlspecialchars($quizName) ?> Sonuçları - Ekoloji Adası</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
body{
       padding-left: 5%;
      padding-right: 5%;
  }
.wrapper {
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.card-body {
    padding: 20px;
}

.table {
    width: 100%;
    margin-top: 20px;
    border-collapse: collapse;
}

.table th, .table td {
    padding: 12px;
    text-align: center;
    border: 1px solid #ddd;
}

.table th {
    background-color: #f8f9fa;
    font-weight: bold;
}

.text-success {
    color: green;
    font-weight: bold;
}

.text-danger {
    color: red;
    font-weight: bold;
}

.text-secondary {
    color: gray;
    font-weight: bold;
}

hr {
    border-top: 2px solid #ddd;
}    </style>
</head>
<body>

<?php include '../header.php'; ?>

    <div class="wrapper">
        <div class="content">
            <h2 class="text-center mb-4">Quiz Sonuçları</h2>

            <!-- Doğru, Yanlış, Boş ve Puan Bilgisi -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Sonuçlar</h5>
                            <p class="text-success">Doğru: <?= $correctCount ?></p>
                            <p class="text-danger">Yanlış: <?= $incorrectCount ?></p>
                            <p class="text-secondary">Boş: <?= $emptyCount ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Puan</h5>
                            <p class="font-weight-bold">Puanımız: <?= $userScore ?> / <?= $totalScore ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Çizgi -->
            <hr class="my-4">

            <!-- En İyi 10 Kullanıcı -->
            <h4 class="text-center mb-3">En İyi 10 Kullanıcı</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kullanıcı Adı</th>
                        <th>Puan</th>
                        <th>Son Deneme</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topUsers as $index => $user): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= $user['score'] ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($user['last_attempt'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>