<?php
require_once dirname(__FILE__, 2) . '/inc/config.php';
require_once FORUM_ROOT . 'inc/dbconnect.php';
session_start();

if (!isset($_SESSION['user_id']) || !($_SESSION['user_id'] === 1)) {
    die();
}

// Ensure the submit button got clicked
if (isset($_POST['submitting']) && $_POST['submitting'] === 'Add Forum') {
    // Make sure forum name was entered
    if (!isset($_POST['forum-name'])) {
        die();
    }
    // Make sure category was entered
    else if (!isset($_POST['category'])) {
        die();
    }

    $pdo = get_pdo();
    $table_prefix = TABLE_PREFIX;
    $stmt = $pdo->prepare("INSERT INTO {$table_prefix}_forums (name, category_id) VALUES (?, ?)");
    if ($stmt->execute([$_POST['forum-name'], $_POST['category']])) {
        echo 'Forum successfully added!';
        header('refresh:2;url=index.php');
        $pdo->commit();
    }
    else {
        $pdo->rollBack();
    }
}