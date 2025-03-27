<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/PHP_PROJECT/includes/config.php');
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT email, profile_picture FROM users WHERE id = :id');
    $stmt->execute([':id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    $error = 'Database error: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($user['email']); ?></h1>
    <p><img src="uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture"></p>
    <a href="logout.php">Logout</a>
</body>
</html>