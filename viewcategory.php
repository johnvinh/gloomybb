<?php
require_once 'inc/config.php';
require_once 'inc/dbconnect.php';
require_once 'classes/Page.php';
require_once 'inc/helpers.php';

session_start();

// Ensure category ID is set
if (!isset($_GET['id'])) {
    echo 'Missing category ID!';
    die();
}

$pdo = get_pdo();
$table_prefix = TABLE_PREFIX;
$stmt = $pdo->prepare("SELECT name FROM {$table_prefix}_categories WHERE id = ?");
$stmt->execute([$_GET['id']]);
$result = $stmt->fetch();

$title = $result['name'];
// Navigation
$links = [
    ['url' => 'index.php', 'name' => 'Index'],
    ['url' => "viewcategory.php?id={$_GET['id']}", 'name' => $result['name']]
];
$navigation = construct_navigation($links);
// Main content
$content = '';
$content .= '<div class="category-block">' . "\n";
// Since this is tabular data, we can use a table instead of CSS grid for lining things up
$content .= '<table>' . "\n";
// Headings
$content .= '<thead><tr>';
$content .= '<th>' . $result['name'] . '</th>';
$content .= '<th>Topics</th>';
$content .= '<th>Posts</th>';
$content .= '</tr></thead>';
$forum_stmt = $pdo->prepare("SELECT * FROM {$table_prefix}_forums WHERE category_id = ?");
$forum_stmt->execute([$_GET['id']]);
$content .= '<tbody>';
foreach ($forum_stmt as $forum_row) {
    $num_topics = $pdo->prepare("SELECT * FROM {$table_prefix}_topics WHERE forum_id = ?");
    $num_topics->execute([$forum_row['id']]);
    $num_posts = $pdo->prepare("SELECT {$table_prefix}_posts.id FROM {$table_prefix}_posts INNER JOIN {$table_prefix}_topics
    ON {$table_prefix}_posts.topic_id = {$table_prefix}_topics.id INNER JOIN {$table_prefix}_forums ON {$table_prefix}_topics.forum_id = {$table_prefix}_forums.id WHERE {$table_prefix}_topics.forum_id = ?");
    $num_posts->execute([$forum_row['id']]);
    $content .= '<tr>';
    $content .= '<td><a href="viewforum.php?id=' . $forum_row['id'] . '">' . $forum_row['name'] . '</a><br>';
    $content .= $forum_row['description'] . '</td>';
    $content .= '<td>' . $num_topics->rowCount() . '</td>';
    $content .= '<td>' . $num_posts->rowCount() . '</td>';
    $content .= '</tr>';
}
$content .= '</tbody>';
$content .= '</table>' . "\n";
$content .= '</div>' . "\n";

$page = new Page($title, $navigation, $content);
$page->write_html();