<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db_config.php';
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT UserID FROM users WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $_SESSION['signup_error'] = "Username already taken. Please choose another.";
        header("Location: signup.php");
        exit;
    } else {
        $stmt = $conn->prepare("INSERT INTO users (Username, Password, DateOfRegistration) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $username, $password);
        
        if ($stmt->execute()) {
            $_SESSION['signup_success'] = "New user registered successfully";
            header("Location: signup.php");
            exit;
        } else {
            $_SESSION['signup_error'] = "Error: " . $stmt->error;
            header("Location: signup.php");
            exit;
        }
    }
    
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
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
        .signup-container {
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
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
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
        .error-message {
            color: red;
            margin-bottom: 10px;
        }
        .success-message {
            color: green;
            margin-bottom: 10px;
        }
        .link-button {
            margin-top: 10px;
            background-color: #6c757d;
        }
        .link-button:hover {
            background-color: #5a6268;
        }
    </style>
    <script>
        function validatePassword() {
            var passwordInput = document.getElementById("password");
            var password = passwordInput.value;
            var passwordPattern = /^(?=.*\d).{8,}$/; // Password should be at least 8 characters long and contain at least one number
            if (!passwordPattern.test(password)) {
                alert("Password must be at least 8 characters long and contain at least one number");
                passwordInput.focus();
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="signup-container">
        <h2>Sign Up</h2>
        <?php
        if (isset($_SESSION['signup_success'])) {
            echo '<p class="success-message">' . $_SESSION['signup_success'] . '</p>';
            unset($_SESSION['signup_success']);
        }
        if (isset($_SESSION['signup_error'])) {
            echo '<p class="error-message">' . $_SESSION['signup_error'] . '</p>';
            unset($_SESSION['signup_error']);
        }
        ?>
        <form action="signup.php" method="post" onsubmit="return validatePassword()">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <input type="submit" value="Sign Up">
        </form>
        <form action="login.php" method="get">
            <input type="submit" class="link-button" value="Go to Login">
        </form>
    </div>
</body>
</html>
