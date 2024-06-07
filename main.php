<?php
session_start();


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.html");
    exit;
}


include 'db_config.php';
$username = $_SESSION['username'];


$stmt = $conn->prepare("SELECT UserID, Username, DateOfRegistration FROM users WHERE Username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($userID, $username, $dateOfRegistration);
$stmt->fetch();
$stmt->close();


$stmt = $conn->prepare("SELECT Result FROM results WHERE User = ? AND Quiz = 1");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($quizResult);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page</title>
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
        p {
            font-size: 1.5em; 
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

        .small-text-button {
            font-size: 24px; 
            height: 80px; 
        }
    </style>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
    <p>Date of Registration: <?php echo htmlspecialchars($dateOfRegistration); ?></p>
    <p>
        <?php
        if (isset($quizResult)) {
            echo "Your score on the first quiz: " . htmlspecialchars($quizResult) . "/10";
        }
        ?>
    </p>
    <div class="button-container">
        <form action="logout.php" method="post">
            <input type="submit" value="Log Out">
        </form>
        <form action="delete_account.php" method="post">
            <input type="submit" value="Delete Account">
        </form>
        <form action="start_page.php" method="get">
            <input type="submit" value="Back to Start Page">
        </form>
    </div>
</body>
</html>