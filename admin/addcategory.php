<?php
require_once dirname(__FILE__, 2) . '/inc/config.php';
require_once FORUM_ROOT . 'inc/dbconnect.php';
session_start();

if (!isset($_SESSION['user_id']) || !($_SESSION['user_id'] === 1)) {
    die();
}

if (isset($_POST['submitting']) && $_POST['submitting'] === 'Add Category') {
    // Make sure the category name is filled
    if (!isset($_POST['category-name'])) {
        die();
    }

    $table_prefix = TABLE_PREFIX;
    $pdo = get_pdo();
    $stmt = $pdo->prepare("INSERT INTO {$table_prefix}_categories (name) VALUES (?)");
    if ($stmt->execute([$_POST['category-name']])) {
        echo 'Category successfully added!';
        header('refresh:2;url=index.php');
        $pdo->commit();
    }
    else {
        $pdo->rollBack();
    }
}