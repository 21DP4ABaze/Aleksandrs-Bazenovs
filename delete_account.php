<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.html");
    exit;
}

include 'db_config.php';

$username = $_SESSION['username'];

$stmt = $conn->prepare("DELETE FROM users WHERE Username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->close();
$conn->close();

session_unset();
session_destroy();

header("Location: login.php");
exit;
?>