<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.html");
    exit;
}

$score = isset($_GET['score']) ? $_GET['score'] : null;
$best = isset($_GET['best']) ? $_GET['best'] : null;
$existing_score = isset($_GET['existing_score']) ? $_GET['existing_score'] : null;

$username = $_SESSION['username'];

$message = "";
if ($score !== null) {
    if ($best == 'true') {
        $message = "Congrats $username, you got $score points!";
    } else {
        $message = "Your score this time: $score points. Your best score: $existing_score points.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .results-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
        }
        .message {
            font-size: 18px;
            margin-bottom: 20px;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="results-container">
        <h2>Quiz Results</h2>
        <p class="message"><?php echo $message; ?></p>
        <form action="start_page.php" method="get">
            <input type="submit" value="Back to Start Page">
        </form>
    </div>
</body>
</html>