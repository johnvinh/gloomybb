<?php
session_start();
require_once 'inc/config.php';
require_once 'inc/dbconnect.php';
require_once 'classes/Page.php';
require_once 'inc/helpers.php';

// Topic ID must be specified in URL
if (!isset($_GET['id'])) {
    echo 'Missing topic ID!';
    die();
}

$table_prefix = TABLE_PREFIX;
$pdo = get_pdo();
$stmt = $pdo->prepare("SELECT * FROM {$table_prefix}_topics WHERE id = ?");
$stmt->execute([$_GET['id']]);
// If the user entered an invalid topic ID
if ($stmt->rowCount() === 0) {
    $navigation = construct_navigation([
        ['url' => 'index.php', 'name' => 'Index']
    ]);
    $page = new Page("No Such Topic", $navigation, "That topic does not exist.");
    $page->write_html();
    die();
}
$result = $stmt->fetch();

$title = htmlspecialchars($result['title']);
// Navigation
$category_stmt = $pdo->prepare("SELECT {$table_prefix}_categories.name, {$table_prefix}_categories.id FROM {$table_prefix}_categories INNER JOIN
    {$table_prefix}_forums ON {$table_prefix}_forums.category_id = {$table_prefix}_categories.id INNER JOIN
    {$table_prefix}_topics ON {$table_prefix}_forums.id = {$table_prefix}_topics.forum_id WHERE {$table_prefix}_topics.id = ?");
$category_stmt->execute([$_GET['id']]);
$category_results = $category_stmt->fetch();
$forum_stmt = $pdo->prepare("SELECT name FROM {$table_prefix}_forums WHERE id = ?");
$forum_stmt->execute([$result['forum_id']]);
$forum_result = $forum_stmt->fetch();

$links = [
    ['url' => 'index.php', 'name' => 'Index'],
    ['url' => "viewcategory.php?id={$category_results['id']}", 'name' => $category_results['name']],
    ['url' => "viewforum.php?id={$result['forum_id']}", 'name' => $forum_result['name']],
    ['url' => "viewtopic.php?id={$_GET['id']}", 'name' => $result['title']]
];
$navigation = construct_navigation($links);
// End Navigation

// Main Content
$content = '<input type="button" id="new-reply" value="New Reply">';
// Topic contents
$content .= '<div class="post">';

// User details
$content .= '<dl>';
$content .= '<dt>Username</dt>';
$user_stmt = $pdo->prepare("SELECT username FROM {$table_prefix}_users WHERE id = ?");
$user_stmt->execute([$result['user_id']]);
$content .= '<dd>' . $user_stmt->fetch()['username'] . '</dd>';
$content .= '<dt>Posted At</dt>';
$content .= '<dd>' . $result['posted_at'] . '</dd>';
$content .= '</dl>';
// Post details
$content .= '<div class="post-content">';
// Delete button
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $result['user_id']) {
    $content .= '<div class="post-actions">';
    $content .= '<a class="button" href="deletetopic.php?id=' . $result['id'] . '">Delete</a>';
    $content .= '</div>';
}
$content .= '<p>' . htmlspecialchars($result['content']) . '</p>';
$content .= '</div>';
// End topic contents
$content .= '</div>';

// Posts
$posts_stmt = $pdo->prepare("SELECT * FROM {$table_prefix}_posts WHERE topic_id = ?");
$posts_stmt->execute([$_GET['id']]);
foreach ($posts_stmt as $post) {
    $content .= '<div id="post' . $post['id'] . '" class="post">';
    // User details
    $content .= '<dl>';
    $content .= '<dt>Username</dt>';
    $user_stmt = $pdo->prepare("SELECT username FROM {$table_prefix}_users WHERE id = ?");
    $user_stmt->execute([$post['user_id']]);
    $content .= '<dd>' . $user_stmt->fetch()['username'] . '</dd>';
    $content .= '<dt>Posted At</dt>';
    $content .= '<dd>' . $post['posted_at'] . '</dd>';
    $content .= '</dl>';
    // Post details
    $content .= '<div class="post-content">';
    // Delete button
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $post['user_id'] || $_SESSION['user_id'] === 1) {
        $content .= '<div class="post-actions">';
        $content .= '<a class="button" href="deletepost.php?id=' . $post['id'] . '">Delete</a>';
        $content .= '</div>';
    }
    $content .= '<p>' . htmlspecialchars($post['content']) . '</p>';
    $content .= '</div>';

    $content .= '</div>';
}
$pdo = null;
// End Main Content
$script = 'scripts/viewtopic.js';
$page = new Page($title, $navigation, $content, $script);
$page->write_html();