<?php
session_start();


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.html");
    exit;
}


include 'db_config.php';

$quiz_id = $_GET['quiz_id'];


$stmt = $conn->prepare("SELECT QuizName, QuizMaximumPoints, QuestionCount FROM quiz WHERE QuizID = ?");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$stmt->bind_result($quiz_name, $quiz_max_points, $question_count);
$stmt->fetch();
$stmt->close();


$stmt = $conn->prepare("SELECT QuestionID, Description, PointForQuestion FROM question WHERE Quiz = ?");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$stmt->bind_result($question_id, $description, $points);
$questions = [];
while ($stmt->fetch()) {
    $questions[] = ['id' => $question_id, 'description' => $description, 'points' => $points];
}
$stmt->close();

foreach ($questions as &$question) {
    $stmt = $conn->prepare("SELECT AnswerID, Answer, Correct FROM answers WHERE Question = ?");
    $stmt->bind_param("i", $question['id']);
    $stmt->execute();
    $stmt->bind_result($answer_id, $answer, $correct);
    $question['answers'] = [];
    while ($stmt->fetch()) {
        $question['answers'][] = ['id' => $answer_id, 'answer' => $answer, 'correct' => $correct];
    }
    $stmt->close();
}
unset($question);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($quiz_name); ?></title>
    <style>

        input[type="radio"] {
            transform: scale(1); 
            margin-right: 10px;
        }


        .question p {
            font-size: 24px; 
            margin-bottom: 10px; 
        }


        .question label {
            font-size: 18px; 
        }


        .submit-button {
            position: fixed;
            bottom: 10px;
            right: 10px;
            width: auto; 
            padding: 10px 20px; 
            border: none;
            border-radius: 5px;
            background-color: #007bff; 
            color: #ffffff; 
            font-size: 20px; 
            cursor: pointer;
        }


        .back-button {
            position: fixed;
            bottom: 10px;
            left: 10px;
            width: auto; 
            padding: 10px 20px; 
            border: none;
            border-radius: 5px;
            background-color: #007bff; 
            color: #ffffff; 
            font-size: 20px; 
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div style="display: flex; justify-content: center; align-items: center; height: 100vh;">
        <div>
            <h2><?php echo htmlspecialchars($quiz_name); ?></h2>
            <form action="save_quiz_results.php" method="post">
                <input type="hidden" name="quiz_id" value="<?php echo htmlspecialchars($quiz_id); ?>">
                <?php foreach ($questions as $index => $question): ?>
                    <div class="question">
                        <p><?php echo ($index + 1) . ". " . htmlspecialchars($question['description']); ?></p>
                        <?php foreach ($question['answers'] as $answer): ?>
                            <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="<?php echo $answer['id']; ?>" required>
                            <label><?php echo htmlspecialchars($answer['answer']); ?></label><br>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
                <input type="submit" value="Submit Quiz" class="submit-button">
            </form>
            <form action="choose_quiz.php" method="get">
                <input type="submit" value="Back to Quiz Chooser" class="back-button">
            </form>
        </div>
    </div>
</body>
</html>