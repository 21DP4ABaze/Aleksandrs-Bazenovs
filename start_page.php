<?php
include 'db_config.php';

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.html");
    exit;
}

$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT UserID, Username FROM users WHERE Username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($userID, $username);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: white; 
            color: black; 
        }
        h2 {
            margin: 20px 0;
            font-size: 2em; 
        }
        .button-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        input[type="submit"], .button {
            width: 400px; 
            height: 80px; 
            margin: 10px 0; 
            border: none;
            border-radius: 10px; 
            background-color: #007bff; 
            color: #ffffff; 
            font-size: 32px; 
            cursor: pointer;
            text-align: center;
        }
        input[type="submit"]:hover, .button:hover {
            background-color: #0056b3; 
        }
    </style>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
    <div class="button-container">
        <form action="main.php" method="get">
            <input type="submit" value="Go to Main Page">
        </form>
        <form action="create_quiz.php" method="get">
            <input type="submit" value="Create a Quiz">
        </form>
        <form action="choose_quiz.php" method="get">
            <input type="submit" value="Choose a Quiz">
        </form>
    </div>
</body>
</html>