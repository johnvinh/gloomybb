<?php
require_once 'inc/config.php';
require_once 'inc/dbconnect.php';
require_once 'classes/Page.php';
require_once 'inc/helpers.php';

session_start();
$table_prefix = TABLE_PREFIX;

// Check if submit button is clicked
if (isset($_POST['posting']) && $_POST['posting'] === 'Post!') {
    // Ensure message isn't empty
    if (empty($_POST['post-content'])) {
        echo 'Missing post message!';
        die();
    }

    // Insert post into DB
    $pdo = get_pdo();
    $stmt = $pdo->prepare("INSERT INTO {$table_prefix}_posts (content, topic_id, user_id) VALUES (?, ?, ?)");
    if ($stmt->execute([$_POST['post-content'], $_POST['topic_id'], $_SESSION['user_id']])) {
        echo 'Post successful!';
        $new_post_id = $pdo->lastInsertId();
        // Redirect back to the topic
        header("Location: viewtopic.php?id={$_POST['topic_id']}#post{$new_post_id}");
        $pdo->commit();
    }
    else
        $pdo->rollback();
    // Close DB connection
    $pdo = null;
    die();
}

// Require a user to be logged in
if (!isset($_SESSION['user_id'])) {
    echo 'You need to be logged in!';
    header('Location: login.php');
    die();
}
// Ensure topic id is specified
else if (!isset($_GET['id'])) {
    echo 'Missing topic ID!';
    die();
}

// Forum details
$pdo = get_pdo();
$stmt = $pdo->prepare("SELECT * FROM {$table_prefix}_forums INNER JOIN {$table_prefix}_topics ON
    {$table_prefix}_topics.forum_id = {$table_prefix}_forums.id WHERE {$table_prefix}_topics.id = ?");
$stmt->execute([$_GET['id']]);
$forum_details = $stmt->fetch();
$category_details = $pdo->prepare("SELECT * FROM {$table_prefix}_categories WHERE id = ?");
$category_details->execute([$forum_details['category_id']]);
$category_details = $category_details->fetch();
$topic_name = $pdo->prepare("SELECT title FROM {$table_prefix}_topics WHERE id = ?");
$topic_name->execute([$_GET['id']]);
$topic_name = $topic_name->fetch();
$title = 'New Reply';

// Navigation
$links = [
    ['url' => 'index.php', 'name' => 'Index'],
    ['url' => "viewcategory.php?id={$category_details['id']}", 'name' => $category_details['name']],
    ['url' => "viewforum.php?id={$forum_details['id']}", 'name' => $forum_details['name']],
    ['url' => "viewtopic.php?id={$_GET['id']}", 'name' => $topic_name['title']]
];
$navigation = construct_navigation($links);

// Main Content
$content = '<form action="newreply.php" method="post">
            <div>
                <label for="post-content">Message</label>
                <textarea id="post-content" name="post-content"></textarea>
            </div>
            <input type="hidden" name="topic_id" value="' . $_GET['id'] . '">
            <input type="submit" name="posting" value="Post!">
        </form>';

// Close DB connection
$pdo = null;

$page = new Page($title, $navigation, $content);
$page->write_html();