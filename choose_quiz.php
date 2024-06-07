<?php
// db_config.php
// Database connection parameters
$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "final_work";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<?php
session_start();


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.html");
    exit;
}


include 'db_config.php';

if (isset($_POST['search'])) {
    $search = $_POST['search'];
    $stmt = $conn->prepare("SELECT QuizID, QuizName FROM quiz WHERE QuizName LIKE ?");
    $search = "%{$search}%";
    $stmt->bind_param("s", $search);
} else {
    $stmt = $conn->prepare("SELECT QuizID, QuizName FROM quiz");
}
$stmt->execute();
$stmt->bind_result($quiz_id, $quiz_name);
$quizzes = [];
while ($stmt->fetch()) {
    $quizzes[] = ['id' => $quiz_id, 'name' => $quiz_name];
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose a Quiz</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            text-align: center;
            margin-bottom: 20px;
        }
        input[type="text"], input[type="submit"] {
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="text"] {
            width: 70%;
        }
        input[type="submit"] {
            width: 20%;
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin-bottom: 10px;
        }
        li a {
            text-decoration: none;
            color: #007bff;
        }
        li a:hover {
            text-decoration: underline;
        }
        .no-results {
            text-align: center;
            margin-top: 20px;
        }
        .back-btn {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Choose a Quiz</h2>
        <form action="" method="post">
            <input type="text" name="search" placeholder="Search for a quiz">
            <input type="submit" value="Search">
        </form>
        <ul>
            <?php if (empty($quizzes)): ?>
                <p class="no-results">No quizzes found.</p>
            <?php else: ?>
                <?php foreach ($quizzes as $quiz): ?>
                    <li><a href="quiz.php?quiz_id=<?php echo $quiz['id']; ?>"><?php echo htmlspecialchars($quiz['name']); ?></a></li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
        <div class="back-btn">
            <form action="start_page.php" method="get">
                <input type="submit" value="Back to Start Page">
            </form>
        </div>
    </div>
</body>
</html>