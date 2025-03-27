<?php
session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/PHP_PROJECT/includes/config.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/PHP_PROJECT/includes/functions.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate inputs
    if (empty($email) || empty($password)) {
        $error = 'Both fields are required.';
    } elseif (!validateEmail($email)) {
        $error = 'Invalid email format.';
    } elseif (!validatePassword($password)) {
        $error = 'Password must be at least 6 characters.';
    }

    if (isset($error)) {
        echo "<p>$error</p>";
    } else {
        try {
            $stmt = $pdo->prepare('SELECT id, password FROM users WHERE email = :email');
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header('Location: profile.php');
                exit;
            } else {
                $error = 'Invalid credentials.';
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/PHP_PROJECT/assets/css/styles.css">

</head>
<body>
    <div class="form-container">
        <h2>Login</h2>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <form method="post">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>
</body>
</html>