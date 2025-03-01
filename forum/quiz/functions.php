<?php

function getAllQuizzes() {
  global $conn;
  $quizzes = [];
  $sql = "SELECT id, title, description FROM quizzes"; 
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $quizzes[] = $row;
    }
  }
  return $quizzes;
}

function getQuizName($quizId) {
    global $conn;
    $sql = "SELECT title FROM quizzes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $quizId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['title'];
}

  function hasUserCompletedQuiz($userId, $quizId)
  {
    global $conn;
    $sql = "SELECT COUNT(*) AS completed
              FROM user_answers 
              WHERE user_id = ? AND quiz_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $quizId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['completed'] > 0;
  }

  function getQuizQuestions($quizId)
  {
    global $conn;
    $sql = "SELECT * FROM questions WHERE quiz_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $quizId);
    $stmt->execute();
    $result = $stmt->get_result();
    $questions = [];
    while ($row = $result->fetch_assoc()) {
      $questionId = $row['id'];
      $answers = getAnswersForQuestion($questionId);
      $questions[] = [
        'id' => $row['id'],
        'question_text' => $row['question_text'],
        'options' => $answers
      ];
    }
    return $questions;
  }

  function getAnswersForQuestion($questionId)
  {
    global $conn;
    $sql = "SELECT * FROM answers WHERE question_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $questionId);
    $stmt->execute();
    $result = $stmt->get_result();
    $answers = [];
    while ($row = $result->fetch_assoc()) {
      $answers[] = [
        'id' => $row['id'],
        'option_text' => $row['answer_text'],
        'is_correct' => $row['is_correct']
      ];
    }
    return $answers;
  }

  function getUserAnswers($userId, $quizId)
  {
    global $conn;
    $sql = "SELECT * FROM user_answers WHERE user_id = ? AND quiz_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $quizId);
    $stmt->execute();
    $result = $stmt->get_result();
    $userAnswers = [];
    while ($row = $result->fetch_assoc()) {
      $userAnswers[$row['question_id']] = $row;
    }
    return $userAnswers;
  }

  function getUserAnswerStatus($userAnswers, $questionId) {
    if (!isset($userAnswers[$questionId])) {
        return 'empty';
    } elseif ($userAnswers[$questionId]['is_correct'] == 1) {
        return 'correct'; 
    } else {
        return 'incorrect'; 
    }
}



function sendNotification($userId, $quizId) {
    global $conn;

    $results = calculateQuizResults($userId, $quizId);
    $correctCount = $results['correct_count'];
    $pointsEarned = $correctCount * 20; 

    $stmt = $conn->prepare("UPDATE users SET point = point + ? WHERE id = ?");
    $stmt->bind_param("ii", $pointsEarned, $userId);
    $stmt->execute();

    $quizName = getQuizName($quizId);

    $notificationText = "{$quizName} başlıklı quizi tamamladınız. {$correctCount} doğru cevapla {$pointsEarned} puan kazandınız!";
    $notificationType = "point_add";
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, notification_text, notification_type) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $notificationText, $notificationType);
    $stmt->execute();
}


function saveUserAnswer($userId, $quizId, $questionId, $selectedAnswer, $isCorrect)
{
  global $conn;
  $sql = "INSERT INTO user_answers (user_id, quiz_id, question_id, selected_answer, is_correct)
            VALUES (?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("iiiii", $userId, $quizId, $questionId, $selectedAnswer, $isCorrect);
  $stmt->execute();
}


function calculateQuizResults($userId, $quizId) {
    global $conn;

    $sql = "SELECT 
                SUM(is_correct = 1) AS correct_count,
                SUM(is_correct = 0) AS incorrect_count,
                SUM(selected_answer IS NULL) AS empty_count
            FROM user_answers
            WHERE user_id = ? AND quiz_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $quizId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
function getTopUsers($quizId) {
    global $conn;

    $sql = "SELECT 
                u.username,
                SUM(ua.is_correct = 1) * 20 AS score,
                MAX(ua.answered_at) AS last_attempt
            FROM user_answers ua
            JOIN users u ON ua.user_id = u.id
            WHERE ua.quiz_id = ?
            GROUP BY ua.user_id
            ORDER BY score DESC, last_attempt DESC
            LIMIT 10";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $quizId);
    $stmt->execute();
    $result = $stmt->get_result();
    $topUsers = [];
    while ($row = $result->fetch_assoc()) {
        $topUsers[] = $row;
    }
    return $topUsers;
}

?>