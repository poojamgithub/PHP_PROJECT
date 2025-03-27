<?php
session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/PHP_PROJECT/includes/config.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/PHP_PROJECT/includes/functions.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $profilePicture = $_FILES['profile_picture'];

    // Validate inputs
     $fileName = "";
    if (empty($email) || empty($password) || empty($confirmPassword)) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } elseif (!validateEmail($email)) {
        $error = 'Invalid email format.';
    } elseif (!validatePassword($password)) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($profilePicture['error'] === 0) {
        if (!validateFile($profilePicture)) {
            $error = 'Invalid file type or size.';
        } else {
            $fileName = uniqid() . '.' . pathinfo($profilePicture['name'], PATHINFO_EXTENSION);
            $filePath = 'uploads/' . $fileName;
            if (!move_uploaded_file($profilePicture['tmp_name'], $filePath)) {
                $error = 'Failed to upload profile picture.';
            }
        }
    }

    if (isset($error)) {
        echo "<p>$error</p>";
    } else {
        try {
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
            $stmt->execute([':email' => $email]);
            if ($stmt->fetch()) {
                $error = 'Email is already registered.';
            } else {
                $hashedPassword = hashPassword($password);
                $stmt = $pdo->prepare('INSERT INTO users (email, password, profile_picture) VALUES (:email, :password, :profile_picture)');
                $stmt->execute([
                    ':email' => $email,
                    ':password' => $hashedPassword,
                    ':profile_picture' => $fileName
                ]);
                $_SESSION['user_id'] = $pdo->lastInsertId();
                header('Location: profile.php');
                exit;
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
    <title>Register</title>
    <link rel="stylesheet" href="/PHP_PROJECT/assets/css/styles.css">
</head>
<body>
    <div class="form-container">
        <h2>Register</h2>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            <div class="form-group">
                <label for="profile_picture">Profile Picture:</label>
                <input type="file" name="profile_picture" id="profile_picture" required>
            </div>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>
</body>
</html>