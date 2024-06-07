<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.html");
    exit;
}

include 'db_config.php';

$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT UserID FROM users WHERE Username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $quiz_id = $_POST['quiz_id'];
    $answers = $_POST['answers'];

    $score = 0;
    foreach ($answers as $question_id => $answer_id) {
        $stmt = $conn->prepare("SELECT Correct FROM answers WHERE AnswerID = ? AND Question = ?");
        $stmt->bind_param("ii", $answer_id, $question_id);
        $stmt->execute();
        $stmt->bind_result($correct);
        $stmt->fetch();
        $stmt->close();
        
        if ($correct) {
            $stmt = $conn->prepare("SELECT PointForQuestion FROM question WHERE QuestionID = ?");
            $stmt->bind_param("i", $question_id);
            $stmt->execute();
            $stmt->bind_result($points);
            $stmt->fetch();
            $stmt->close();
            $score += $points;
        }
    }

    $stmt = $conn->prepare("SELECT ResultID, Result FROM results WHERE User = ? AND Quiz = ?");
    $stmt->bind_param("ii", $user_id, $quiz_id);
    $stmt->execute();
    $stmt->bind_result($result_id, $existing_score);
    
    if ($stmt->fetch()) {
        $stmt->close();
        if ($score > $existing_score) {
            $stmt = $conn->prepare("UPDATE results SET Result = ? WHERE ResultID = ?");
            $stmt->bind_param("ii", $score, $result_id);
            $stmt->execute();
            $stmt->close();
            header("Location: results.php?score=$score&best=true");
            exit;
        } else {
            header("Location: results.php?score=$score&best=false&existing_score=$existing_score");
            exit;
        }
    } else {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO results (Quiz, User, Result) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $quiz_id, $user_id, $score);
        $stmt->execute();
        $stmt->close();
        header("Location: results.php?score=$score&best=true");
        exit;
    }
}
?>
