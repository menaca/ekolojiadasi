<?php

require '../netting/connect.php';
require 'functions.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Geçersiz veya eksik Quiz ID!");
}

if (isset($_GET['id'])) {
  $userId = $_SESSION['user_id']; 
  $quizId = $_GET['id']; 

  $gifFiles = ['plant1.gif', 'plant_2.gif', 'plant_3.gif', 'plant_4.gif', 'plant_5.gif'];

  $isCompleted = hasUserCompletedQuiz($userId, $quizId);
  $questions = getQuizQuestions($quizId);
  $userAnswers = $isCompleted ? getUserAnswers($userId, $quizId) : [];
  $quizName = getQuizName($quizId);

  if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$isCompleted) {
        $answeredQuestions = 0;
        $totalQuestions = count($questions);

        foreach ($_POST['answers'] as $questionId => $selectedAnswer) {
            $sql = "SELECT is_correct FROM answers WHERE question_id = ? AND id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $questionId, $selectedAnswer);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $isCorrect = $row['is_correct'] == 1;

            saveUserAnswer($userId, $quizId, $questionId, $selectedAnswer, $isCorrect);
            $answeredQuestions++;
        }
        
        if ($answeredQuestions === $totalQuestions) {
            sendNotification($userId, $quizId);
        }    
    
        header("Location: result.php?id={$quizId}");
        exit();
  }
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
  <title><?= htmlspecialchars($quizName) ?> - Ekoloji Adası</title>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <style>
 @import url('https://fonts.googleapis.com/css2?family=Open+Sans&display=swap');

body {
  font-family: 'Open Sans', sans-serif;
  background: #eee;
    padding-left: 5%;
      padding-right: 5%;
}

.wrapper {
  max-width: 600px;
  margin: 20px auto;
  position: relative;
  min-height: 650px;
  overflow: hidden;
  border: 1px solid #ccc;
  background: #fff;
}

.content {
  background: #fff;
  border-radius: 5px;
  width: 100%;
  position: absolute;
  top: 50%;
  transform: translateY(-50%) translateX(0);
  transition: transform 0.5s ease;
  padding: 20px;
  padding-bottom: 50px;
}

a:hover {
  text-decoration: none;
}

a,
span {
  font-size: 15px;
  font-weight: 600;
  color: rgb(50, 141, 245);
  padding-bottom: 30px;
}

p.text-muted {
  font-size: 14px;
  font-weight: 500;
  margin-bottom: 5px;
}

b {
  font-size: 15px;
  font-weight: bolder;
}

.option {
  display: block;
  background-color: #f4f4f4;
  position: relative;
  width: 100%;
}

.option:hover {
  background-color: #e8e8e8;
  cursor: pointer;
}

.option input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

.option {
      display: block;
      padding: 10px;
      margin: 5px 0;
      border: 2px solid #ccc;
      border-radius: 5px;
      cursor: pointer;
    }

    .option.disabled {
      cursor: not-allowed;
    }

    .option.correct {
      border-color: green;
      background-color: #e6ffe6;
    }

    .option.incorrect {
      border-color: red;
      background-color: #ffe6e6;
    }

    .option.correct-answer {
      border-color: green;
      background-color: #e6ffe6;
    }

    input[type="radio"]:disabled {
      cursor: not-allowed;
    } 

.checkmark,
.crossmark {
  position: absolute;
  top: 10px;
  right: 10px;
  height: 22px;
  width: 22px;
  background-color: #f4f4f4;
  border-radius: 2px;
  padding: 0;
}

p.mb-4 {
  border-left: 3px solid green;
}

p.my-2 {
  border-left: 3px solid red;
}

input[type="submit"] {
  width: 100%;
  height: 50px;
  background-color: #229aeb;
  border: none;
  outline: none;
  color: #fff;
  font-weight: 600;
  cursor: pointer;
}

input[type="submit"]:hover:focus {
  background-color: #229bebad;
}

#questionContainer {
  transform: translateY(-50%) translateX(0);
}

#gifContainer {
  transform: translateY(-50%) translateX(100%);
  display: none;
  text-align: center;
}

.questionContainer {
  display: none;
}

.questionContainer.active {
  display: block;
}
</style>
</head>

<body>
  <div class="wrapper">
  
  <form method="POST" action="quiz.php?id=<?php echo $quizId?>">
      <?php foreach ($questions as $index => $question): ?>
        <div class="content questionContainer<?php echo ($index == 0) ? ' active' : ''; ?>">
          <a href="/index.php"><span class="fa fa-angle-left pr-2"></span>Anasayfaya dön</a>
          <p class="text-muted"><?= htmlspecialchars($quizName) ?></p>
          <p class="text-justify h5 pb-2 font-weight-bold">
            <?= htmlspecialchars($question['question_text']) ?>
          </p>

          <?php if ($isCompleted): ?>
            <?php
            $status = getUserAnswerStatus($userAnswers, $question['id']);
            $statusText = '';
            $statusClass = '';
            if ($status === 'correct') {
                $statusText = 'Bu soruyu doğru cevaplandırdınız.';
                $statusClass = 'text-success';
            } elseif ($status === 'incorrect') {
                $statusText = 'Bu soruyu yanlış cevaplandırdınız.';
                $statusClass = 'text-danger';
            } else {
                $statusText = 'Bu soruyu boş bıraktınız.';
                $statusClass = 'text-secondary';
            }
            ?>
            <p class="font-weight-bold <?= $statusClass ?> mt-3">
                <?= $statusText ?>
            </p>
        <?php endif; ?>


          <div class="options py-3">
            <?php foreach ($question['options'] as $option): ?>
              <?php
              $isSelected = isset($userAnswers[$question['id']]) && $userAnswers[$question['id']]['selected_answer'] == $option['id'];
              $isCorrect = $isSelected && $userAnswers[$question['id']]['is_correct'];
              $isIncorrect = $isSelected && !$userAnswers[$question['id']]['is_correct'];
              $disabled = $isCompleted ? 'disabled' : '';
              $class = '';
              if ($isCompleted) {
                if ($isCorrect) {
                  $class = 'correct';
                } elseif ($isIncorrect) {
                  $class = 'incorrect';
                }
                if ($option['is_correct'] == 1) {
                  $class = 'correct-answer';
                }
              }
              ?>
              <label class="rounded p-2 option <?= $class ?> <?= $disabled ? 'disabled' : '' ?>">
                <input type="radio" name="answers[<?= $question['id'] ?>]" value="<?= $option['id'] ?>" 
                       <?= $isSelected ? 'checked' : '' ?> <?= $disabled ?> required>
                <?= htmlspecialchars($option['option_text']) ?>
              </label>
            <?php endforeach; ?>
          </div>
          <?php if (!$isCompleted): ?>
            <input type="submit" value="<?php echo ($index == count($questions) - 1) ? 'Gönder' : 'Devam'; ?>"
                   class="mx-sm-0 mx-1 submitBtn">
          <?php else: ?>
            <input  type="submit" value="<?php echo ($index == count($questions) - 1) ? 'Sonuçlar' : 'İleri'; ?>"
            class="mx-sm-0 mx-1 submitBtn">
          <?php endif; ?>

        </div>
      <?php endforeach; ?>
    </form>

    <div id="gifContainer" class="content" style="display: none; text-align: center;" data-gifs='<?= json_encode($gifFiles) ?>'>
     <img src="" alt="GIF Animation" style="max-width: 100%;">
    </div>

    <div id="resultContainer" class="content" style="display: none;">
      <h2>Bekleyin..</h2>
  </div>

  <script>  
  document.addEventListener('DOMContentLoaded', function () {
  const options = document.querySelectorAll('.option input');

  options.forEach(option => {
    option.addEventListener('change', function () {
      // Tüm şıklardaki çerçeveleri kaldır
      options.forEach(opt => {
        opt.parentElement.style.border = 'none';
      });

      // Seçilen şıkkın etrafına mavi çerçeve ekle
      if (this.checked) {
        this.parentElement.style.border = '2px solid #229aeb';
        this.parentElement.style.borderRadius = '5px';
        this.parentElement.style.padding = '8px';
      }
    });
  });

  const questionContainers = document.querySelectorAll('.questionContainer');
  const resultContainer = document.getElementById('resultContainer');
  const totalQuestions = questionContainers.length;

  const gifContainer = document.getElementById('gifContainer');
  const gifFiles = JSON.parse(gifContainer.getAttribute('data-gifs'));
  let currentGifIndex = 0;

  // Yardımcı fonksiyonlar: Gösterme / Gizleme animasyonu
  function showContainer(container) {
    container.style.display = 'block';
    container.style.transform = 'translateY(-50%) translateX(100%)';
    // Reflow için
    container.offsetWidth;
    container.style.transform = 'translateY(-50%) translateX(0)';
  }

  function hideContainer(container, callback) {
    container.style.transform = 'translateY(-50%) translateX(-100%)';
    setTimeout(function () {
      container.style.display = 'none';
      if (callback) callback();
    }, 500);
  }

  // Her soru ekranındaki buton için olay dinleyici
  questionContainers.forEach((qContainer, index) => {
    const submitBtn = qContainer.querySelector('.submitBtn');
    submitBtn.addEventListener('click', function (e) {
      e.preventDefault();

      hideContainer(qContainer, function () {

        const gifImg = gifContainer.querySelector('img');
        gifImg.src = '/images/' + gifFiles[currentGifIndex];
        currentGifIndex = (currentGifIndex + 1) % gifFiles.length;

        showContainer(gifContainer);
        // 3 saniye GIF gösterildikten sonra...
        setTimeout(function () {
          hideContainer(gifContainer, function () {
            // Eğer son soru ise formu gönder, değilse bir sonraki soru ekranını göster
            if (index === totalQuestions - 1) {
              // Formu gönder
              document.querySelector('form').submit();
            } else {
              // Mevcut aktif sınıfı kaldır, sonraki soruya geç
              qContainer.classList.remove('active');
              const nextContainer = questionContainers[index + 1];
              nextContainer.classList.add('active');
              showContainer(nextContainer);
            }
          });
        }, 2000);
      });
    });
  });

  document.getElementById('exitBtn').addEventListener('click', function () {
    window.location.href = 'exit_page.php';
  });
});
  </script>
</body>

</html>