<?php
session_start();
require_once 'inc/config.php';
require_once 'inc/dbconnect.php';
require_once 'classes/Page.php';
require_once 'inc/helpers.php';

$table_prefix = TABLE_PREFIX;

// Check if submit button was clicked
if (isset($_POST['newtopic']) && $_POST['newtopic'] === "Post") {
    // Make sure topic has a title
    if (empty($_POST['topic-name'])) {
        echo 'Missing topic name!';
        die();
    }
    // Make sure topic has a body
    else if (empty($_POST['topic-content'])) {
        echo 'Missing topic content!';
        die();
    }

    // Create the topic in the DB
    $pdo = get_pdo();
    $stmt = $pdo->prepare("INSERT INTO {$table_prefix}_topics (title, content, forum_id, user_id) VALUES
(?, ?, ?, ?)");
    if ($stmt->execute([$_POST['topic-name'], $_POST['topic-content'], $_POST['forum_id'], $_SESSION['user_id']])) {
        $new_topic_id =  $pdo->lastInsertId();
        $pdo->commit();
        header("Location: viewtopic.php?id=${new_topic_id}");
        die();
    }
    else {
        echo 'Failed to create topic!';
        die();
    }
}

// Ensure user is logged in before continuing
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    die();
}
// Ensure there's a forum id
else if (!isset($_GET['forum_id'])) {
    echo 'No forum specified!';
    header('refresh:2;url=index.php');
    die();
}

$title = 'New Topic';
// Forum and category details
$pdo = get_pdo();
$stmt = $pdo->prepare("SELECT * FROM {$table_prefix}_forums INNER JOIN {$table_prefix}_topics ON
    {$table_prefix}_forums.id = {$table_prefix}_topics.forum_id WHERE {$table_prefix}_forums.id = ?");
$stmt->execute([$_GET['forum_id']]);
$forum_details = $stmt->fetch();
$category_name = $pdo->prepare("SELECT * FROM {$table_prefix}_categories WHERE id = ?");
$category_name->execute([$forum_details['category_id']]);
$category_name = $category_name->fetch();

// Navigation
$links = [
    ['url' => 'index.php', 'name' => 'Index'],
    ['url' => "viewcategory.php?id={$category_name['id']}", 'name' => $category_name['name']],
    ['url' => "viewforum.php?id={$forum_details['id']}", 'name' => $forum_details['name']]
];
$navigation = construct_navigation($links);

// End Navigation

$content = '<form action="newtopic.php" method="post">
            <div>
                <label for="topic-name">Topic Name</label>
                <input type="text" id="topic-name" name="topic-name">
            </div>
            <div>
                <label for="topic-content">Message</label>
               <textarea id="topic-content" name="topic-content"></textarea>
            </div>
            <input type="hidden" value="' . $_GET['forum_id'] . '" name="forum_id">
            <input type="submit" name="newtopic" value="Post">
        </form>';

$page = new Page($title, $navigation, $content, "scripts/newtopic.js");
$page->write_html();