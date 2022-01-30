<?php
require_once 'inc/config.php';
require_once 'classes/Page.php';
require_once 'inc/helpers.php';
session_start();

$title = 'Index';
$navigation = '<a href="index.php">Index</a>';
$content = '';

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
    $content .= '<th><a href="viewcategory.php?id=' . $category_id . '">'. $category_name . '</a></th>';
    $content .= '<th>Topics</th>';
    $content .= '<th>Posts</th>';
    $content .= '</tr></thead>';
    $forum_stmt = $pdo->prepare("SELECT * FROM {$table_prefix}_forums WHERE category_id = ?");
    $forum_stmt->execute([$category_id]);
    $content .= '<tbody>';
    foreach ($forum_stmt as $forum_row) {
        $num_topics = $pdo->prepare("SELECT * FROM {$table_prefix}_topics WHERE forum_id = ?");
        $num_topics->execute([$forum_row['id']]);
        $num_posts = $pdo->prepare("SELECT {$table_prefix}_posts.id FROM {$table_prefix}_posts INNER JOIN {$table_prefix}_topics
    ON {$table_prefix}_posts.topic_id = {$table_prefix}_topics.id INNER JOIN {$table_prefix}_forums ON {$table_prefix}_topics.forum_id = {$table_prefix}_forums.id WHERE {$table_prefix}_topics.forum_id = ?");
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