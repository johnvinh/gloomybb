<?php
// EST
date_default_timezone_set("America/New_York");

require_once 'inc/config.php';
require_once 'inc/dbconnect.php';
require_once 'classes/Page.php';
require_once 'inc/helpers.php';

$title = 'Sign Up';
$links = [
    ['url' => 'index.php', 'name' => 'Index'],
    ['url' => 'signup.php', 'name' => 'Sign Up']
];
$navigation = construct_navigation($links);

// Submit button was clicked
if (isset($_POST['signup']) && $_POST['signup'] === "Sign-up") {
    // Ensure username was entered
    if (empty($_POST['username'])) {
        echo 'Missing username!';
        die();
    }
    // Ensure password was entered
    else if (empty($_POST['password'])) {
        echo 'Missing password!';
        die();
    }

    // Ensure username is alphanumeric only
    if (!preg_match('/^[A-Za-z0-9]+?$/', $_POST['username'])) {
        echo 'That username contains illegal characters!';
        die();
    }

    // Check to make sure that the username isn't taken
    $table_prefix = TABLE_PREFIX;
    $pdo = get_pdo();
    // Ignore casing
    $stmt = $pdo->prepare("SELECT id FROM {$table_prefix}_users WHERE LOWER(username) = LOWER(?)");
    $stmt->execute([$_POST['username']]);
    if ($stmt->rowCount() > 0) {
        echo 'That username is already taken!';
        die();
    }
    // Since the username isn't taken, we can safely create this user
    $stmt = $pdo->prepare("INSERT INTO {$table_prefix}_users (username, password, joined_at) VALUES (?, ?, ?)");
    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    if ($stmt->execute([$_POST['username'], $hashed_password, date('Y-m-d H:i:s')])) {
        echo 'Registration successful!';
        $pdo->commit();
        header('refresh:2;url=index.php');
    }
    else {
        echo 'An error occurred during registration!';
        $pdo->rollBack();
        die();
    }
}

$content = '<form action="signup.php" method="post">
            <div>
                <label for="username">Username</label>
                <input type="text" id="username" name="username">
            </div>
            <div>
                <label for="password">Password</label>
                <input type="password" id="password" name="password">
            </div>
            <input type="submit" value="Sign-up" name="signup">
        </form>';

$page = new Page($title, $navigation, $content, "scripts/login.js");
$page->write_html();