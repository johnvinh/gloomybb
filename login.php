<?php
require_once 'inc/config.php';
require_once 'inc/dbconnect.php';
require_once 'classes/Page.php';
require_once 'inc/helpers.php';

$content = "";

// Check if the user was referred from the viewtopic page
if (isset($_SERVER['HTTP_REFERER']) && str_contains($_SERVER['HTTP_REFERER'], "viewtopic.php")) {
    $content .= "You need to be logged in to post replies.";
}
// Or from the newtopic page
else if (isset($_SERVER['HTTP_REFERER']) && str_contains($_SERVER['HTTP_REFERER'], "viewforum.php")) {
    $content .= "You need to be logged into post new topics.";
}

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

    $stmt = $pdo->prepare("SELECT id, password FROM {$table_prefix}_users WHERE username = ?");
    $stmt->execute([$_POST['username']]);
    // No users found
    if ($stmt->rowCount() === 0) {
        echo 'No users found with that username found!';
        die();
    }
    $results = $stmt->fetch();
    $hashed_password = $results['password'];
    // Incorrect password entered
    if (!password_verify($_POST['password'], $hashed_password)) {
        echo 'Incorrect password!';
        die();
    }
    // Valid login, so we can start setting up the session
    session_start();
    $_SESSION['username'] = $_POST['username'];
    $_SESSION['user_id'] = $results['id'];

    echo 'Login successful! Redirecting to homepage...';
    header("refresh:2;url=index.php");
    // Close database connection
    $pdo = null;
}

$title = 'Login';
// Nevigation
$links = [
    ['url' => 'index.php', 'name' => 'Index'],
    ['url' => 'login.php', 'name' => 'Login']
];
$navigation = construct_navigation($links);

// Main Page Content
$content .= '<form action="login.php" method="post">
            <div>
                <label for="username">Username</label>
                <input type="text" id="username" name="username">
            </div>
            <div>
                <label for="password">Password</label>
                <input type="password" id="password" name="password">
            </div>
            <input type="submit" name="login" value="Login!">
        </form>';

$page = new Page($title, $navigation, $content);
$page->write_html();