<?php
require_once 'inc/config.php';
require_once 'inc/dbconnect.php';

// If the user clicked the submit button
if (isset($_POST['login']) && $_POST['login'] === "Login!") {
    // Need to use empty() instead of isset() since clicking the button will always result
    // in these POST variables getting set
    if (empty($_POST['username'])) {
        echo 'Missing username!';
        die();
    }
    else if (empty($_POST['password'])) {
        echo 'Missing password!';
        die();
    }

    // Validate username
    if (!preg_match('/^[A-Za-z0-9]+?$/', $_POST['username'])) {
        echo 'That username contains illegal characters.';
        die();
    }

    // All required data has been entered
    $pdo = get_pdo();
    $table_prefix = TABLE_PREFIX;

    $stmt = $pdo->prepare("SELECT password FROM {$table_prefix}_users WHERE username = ?");
    $stmt->execute([$_POST['username']]);
    // No users found
    if ($stmt->rowCount() === 0) {
        echo 'No users found with that username found!';
        die();
    }
    $hashed_password = $stmt->fetch()['password'];
    // Incorrect password entered
    if (!password_verify($_POST['password'], $hashed_password)) {
        echo 'Incorrect password!';
        die();
    }
    // Valid login, so we can start setting up the session
    session_start();
    $_SESSION['username'] = $_POST['username'];

    echo 'Login successful! Redirecting to homepage...';
    header("refresh:2;url=index.php");
    // Close database connection
    $pdo = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo FORUM_NAME; ?> - Login</title>
    <meta charset="utf-8">
</head>
<body>
<div id="content">
    <header>
        <h1><?php echo FORUM_NAME; ?></h1>
    </header>
    <main>
        <form action="login.php" method="post">
            <div>
                <label for="username">Username</label>
                <input type="text" id="username" name="username">
            </div>
            <div>
                <label for="password">Password</label>
                <input type="password" id="password" name="password">
            </div>
            <input type="submit" name="login" value="Login!">
        </form>
    </main>
</div>
</body>
</html>
