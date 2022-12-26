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
// Make post date human-readable
$post_date = new DateTime($result['posted_at']);
$post_date = $post_date->format('F j, Y, g:i a');
// User details
$user_stmt = $pdo->prepare("SELECT * FROM {$table_prefix}_users WHERE id = ?");
$user_stmt->execute([$result['user_id']]);
$user_result = $user_stmt->fetch();
// Make join date more readable
$join_date = new DateTime($user_result['joined_at']);
$join_date = $join_date->format('F j, Y');
$content .= '<div class="user-details">';
$content .= '<h3>' . htmlspecialchars($user_result['username']) . '</h3>';
$content .= '<p>Joined: ' . $join_date . '</p>';
$content .= '</div>';
// Post details
$content .= '<div class="post-content">';
$content .= '<span class="post-date">' . $post_date . '</span>';
// Delete button
if ((isset($_SESSION['user_id']) && ($_SESSION['user_id'] === $result['user_id']))
    || (isset($_SESSION['user_id']) && $_SESSION['user_id'] === 1)) {
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
    // Make post date human-readable
    $date = new DateTime($post['posted_at']);
    $date = $date->format('F j, Y, g:i a');
    // User details
    $user_stmt = $pdo->prepare("SELECT * FROM {$table_prefix}_users WHERE id = ?");
    $user_stmt->execute([$result['user_id']]);
    $user_stmt = $user_stmt->fetch();
    $join_date = new DateTime($user_stmt['joined_at']);
    // Make join date more readable
    $join_date = $join_date->format('F j, Y');
    $content .= '<div class="user-details">';
    $content .= '<h3>' . $user_stmt['username'] . '</h3>';
    $content .= '<p>Joined: ' . $join_date . '</p>';
    $content .= '</div>';
    // Post details
    $content .= '<div class="post-content">';
    $content .= '<span class="post-date">' . $date . '</span>';
    // Delete button
    if ((isset($_SESSION['user_id']) && $_SESSION['user_id'] === $post['user_id'])
        || (isset($_SESSION['user_id']) && $_SESSION['user_id'] === 1)) {
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
$page = new Page($title, $navigation, $content, $script, "viewtopic.css");
$page->write_html();