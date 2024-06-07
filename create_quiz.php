<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location:index.html");
    exit;
}
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $quiz_name = $_POST['quiz_name'];

    $stmt = $conn->prepare("INSERT INTO quiz (QuizName) VALUES (?)");
    $stmt->bind_param("s", $quiz_name);
    $stmt->execute();
    $quiz_id = $stmt->insert_id;
    $stmt->close();

    $stmtQuestion = $conn->prepare("INSERT INTO question (Description, Quiz, PointForQuestion) VALUES (?, ?, ?)");
    $stmtAnswer = $conn->prepare("INSERT INTO answers (Question, Answer, Correct) VALUES (?, ?, ?)");

    foreach ($_POST['questions'] as $questionData) {
        $question = $questionData['question'];
        $points = $questionData['points'];
        $answers = $questionData['answers'];

        $stmtQuestion->bind_param("sii", $question, $quiz_id, $points);
        $stmtQuestion->execute();
        $question_id = $stmtQuestion->insert_id;

        foreach ($answers as $index => $answer) {
            $is_correct = ($index == $questionData['correct_answer']) ? 1 : 0;
            $stmtAnswer->bind_param("isi", $question_id, $answer, $is_correct);
            $stmtAnswer->execute();
        }
    }

    $stmtQuestion->close();
    $stmtAnswer->close();
    $conn->close();

    header("Location: start_page.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Quiz</title>
</head>
<body>
    <div style="display: flex; justify-content: center; align-items: center; height: 100vh;">
        <div>
            <h2>Create a Quiz</h2>
            <form id="quizForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <label>Quiz Name:</label><br>
                <input type="text" name="quiz_name" required><br>
                
                <div id="questions">
                    <div class="question">
                        <label>Question:</label><br>
                        <input type="text" name="questions[0][question]" required><br>
                        
                        <label>Points:</label><br>
                        <input type="number" name="questions[0][points]" value="1" min="1" required><br>
                        
                        <label>Answers:</label><br>
                        <input type="text" name="questions[0][answers][]" required><br>
                        <input type="text" name="questions[0][answers][]" required><br>
                        <input type="text" name="questions[0][answers][]" required><br>
                        <input type="text" name="questions[0][answers][]" required><br>
                        
                        <label>Correct Answer:</label><br>
                        <select name="questions[0][correct_answer]">
                            <option value="0">Answer 1</option>
                            <option value="1">Answer 2</option>
                            <option value="2">Answer 3</option>
                            <option value="3">Answer 4</option>
                        </select><br>
                    </div>
                </div>

                <button type="button" onclick="addQuestion()">Add Question</button>
                <input type="submit" value="Create Quiz">
            </form>
            <form action="start_page.php" method="get">
                <input type="submit" value="Back to Start Page">
            </form>
        </div>
    </div>

    <script>
        let questionCount = 1;

        function addQuestion() {
            questionCount++;

            let questionDiv = document.createElement("div");
            questionDiv.className = "question";

            questionDiv.innerHTML = `
                <label>Question:</label><br>
                <input type="text" name="questions[${questionCount}][question]" required><br>
                
                <label>Points:</label><br>
                <input type="number" name="questions[${questionCount}][points]" value="1" min="1" required><br>
                
                <label>Answers:</label><br>
                <input type="text" name="questions[${questionCount}][answers][]" required><br>
                <input type="text" name="questions[${questionCount}][answers][]" required><br>
                <input type="text" name="questions[${questionCount}][answers][]" required><br>
                <input type="text" name="questions[${questionCount}][answers][]" required><br>
                
                <label>Correct Answer:</label><br>
                <select name="questions[${questionCount}][correct_answer]">
                    <option value="0">Answer 1</option>
                    <option value="1">Answer 2</option>
                    <option value="2">Answer 3</option>
                    <option value="3">Answer 4</option>
                </select><br>
                <button type="button" onclick="removeQuestion(this)">Remove</button>
            `;

            document.getElementById("questions").appendChild(questionDiv);
        }

        function removeQuestion(button) {
            let questionDiv = button.parentElement;
            questionDiv.parentElement.removeChild(questionDiv);
        }
    </script>
</body>
</html>