<?php
require_once 'inc/config.php';
require_once 'classes/Page.php';
require_once 'inc/helpers.php';
session_start();

$title = 'Index';
$navigation = '<a href="index.php">Index</a>';
$content = '';
// Login/logout
if (isset($_SESSION['username'])) {
    $content .= '<p>You are logged in as ' . $_SESSION['username'] . '</p>
        <p><a href="logout.php">Log-out</a></p>';
}
else {
    $content .= '<p><a href="login.php">Login</a></p>
            <p><a href="signup.php">Sign-up</a></p>';
}

// Navigation
$links = [
    ['url' => 'index.php', 'name' => 'Index']
];
$navigation = construct_navigation($links);

// Main page content
require_once 'inc/dbconnect.php';
$pdo = get_pdo();
$table_prefix = TABLE_PREFIX;

$stmt = $pdo->prepare("SELECT * FROM {$table_prefix}_categories");
$stmt->execute();
// Each category should have its own block of forums
foreach ($stmt as $row) {
    $category_id = $row['id'];
    $category_name = $row['name'];
    $content .= '<div class="category-block">' . "\n";
    // Since this is tabular data, we can use a table instead of CSS grid for lining things up
    $content .= '<table>' . "\n";
    // Headings
    $content .= '<thead><tr>';
    $content .= '<th>' . $category_name . '</th>';
    $content .= '<th>Topics</th>';
    $content .= '<th>Posts</th>';
    $content .= '</tr></thead>';
    $forum_stmt = $pdo->prepare("SELECT * FROM {$table_prefix}_forums WHERE category_id = ?");
    $forum_stmt->execute([$category_id]);
    $content .= '<tbody>';
    foreach ($forum_stmt as $forum_row) {
        $num_topics = $pdo->prepare("SELECT * FROM {$table_prefix}_topics WHERE forum_id = ?");
        $num_topics->execute([$forum_row['id']]);
        $num_posts = $pdo->prepare("SELECT 123_posts.id FROM 123_posts INNER JOIN 123_topics
    ON 123_posts.topic_id = 123_topics.id INNER JOIN 123_forums ON 123_topics.forum_id = 123_forums.id WHERE 123_topics.forum_id = ?");
        $num_posts->execute([$forum_row['id']]);
        $content .= '<tr>';
        $content .= '<td><a href="viewforum.php?id=' . $forum_row['id'] . '">' . $forum_row['name'] . '</a></td>';
        $content .= '<td>' . $num_topics->rowCount() . '</td>';
        $content .= '<td>' . $num_posts->rowCount() . '</td>';
        $content .= '</tr>';
    }
    $content .= '</tbody>';
    $content .= '</table>' . "\n";
    $content .= '</div>' . "\n";
}
// Close DB connection
$pdo = null;

$page = new Page($title, $navigation, $content);
$page->write_html();