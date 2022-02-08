<?php
require_once 'classes/Page.php';
require_once 'inc/dbconnect.php';
require_once 'inc/helpers.php';

session_start();
$table_prefix = TABLE_PREFIX;
$content = '';
$pdo = get_pdo();
$navigation = construct_navigation([
    ['url' => 'index.php', 'name' => 'Index']
]);

// Check if we're deleting the post
if (isset($_POST['delete']) && $_POST['delete'] === "Yes") {
    // Delete the associated posts first
    $stmt = $pdo->prepare("DELETE FROM {$table_prefix}_posts WHERE topic_id = ?");
    if (!$stmt->execute([$_POST['topic_id']])) {
        $page = new Page("Delete Topic", $navigation, "Failed to delete topic!");
        $pdo->rollBack();
        $page->write_html();
        $pdo = null;
        die();
    }
    $stmt = $pdo->prepare("DELETE FROM {$table_prefix}_topics WHERE id = ?");
    if ($stmt->execute([$_POST['topic_id']])) {
        $page = new Page("Delete Topic", $navigation, "Topic successfully deleted!");
        $page->write_html();
        $pdo->commit();
        $pdo = null;
        header("refresh:2;url=index.php");
        die();
    }
    else {
        $page = new Page("Delete Topic", $navigation, "Failed to delete topic!");
        $page->write_html();
        $pdo->rollBack();
        $pdo = null;
        die();
    }
}
$id = $_GET['id'];
// Require a post to delete
if (!isset($_GET['id'])) {
    header('refresh:5;url=index.php');
    $page = new Page("Delete Post", $navigation, "You need to specify a topic to delete.");
    $page->write_html();
    die();
}
if (!($id = filter_var($id, FILTER_VALIDATE_INT))) {
    $page = new Page("Delete Post", $navigation, "The ID must be an integer.");
    $page->write_html();
    die();
}

// Check if that post exists
$stmt = $pdo->prepare("SELECT * FROM {$table_prefix}_topics WHERE id = ?");
$stmt->execute([$_GET['id']]);
$result = $stmt->fetch();

if ($stmt->rowCount() === 0) {
    $page = new Page("Delete Topic", $navigation, "That topic does not exist.");
    $page->write_html();
    die();
}

// Make sure that the user is logged in and owns that post
if (!isset($_SESSION['user_id'])) {
    $page = new Page("Delete Topic", $navigation, "You are not logged in!");
    $page->write_html();
    die();
}
else if ($_SESSION['user_id'] !== $result['user_id'] && $_SESSION['user_id'] !== 1) {
    $page = new Page("Delete Topic", $navigation, "You do not have permission to delete that!");
    $page->write_html();
    die();
}

$content .= '
<p>Are you sure you want to delete this topic?</p>
<form method="post" action="deletetopic.php">
<input type="hidden" name="topic_id" value="' . $id . '">
<input type="submit" value="Yes" name="delete">
</form>
';

$page = new Page("Delete Topic", $navigation, $content);
$page->write_html();
// Close DB connection
$pdo = null;