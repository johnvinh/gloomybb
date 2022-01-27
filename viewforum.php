<?php
require_once 'inc/config.php';
require_once 'inc/dbconnect.php';
require_once 'classes/Page.php';
require_once 'inc/helpers.php';

$table_prefix = TABLE_PREFIX;
$pdo = get_pdo();

if (empty($_GET))
    die();
$id = $_GET['id'];
// Ensure that only integers get passed as a parameter
if (!($id = filter_var($id, FILTER_VALIDATE_INT)))
    die();
// Forum ID is passed as a GET parameter
$subforum_name = $pdo->prepare("SELECT name, category_id FROM {$table_prefix}_forums WHERE id = ?");
$subforum_name->execute([$_GET['id']]);
$forum_result = $subforum_name->fetch();
$title = $forum_result['name'];
// Navigation
$category_stmt = $pdo->prepare("SELECT name FROM {$table_prefix}_categories WHERE id = ?");
$category_stmt->execute([$forum_result['category_id']]);
$category_name = $category_stmt->fetch()['name'];

$links = [
    ['url' => 'index.php', 'name' => 'Index'],
    ['url' => "viewcategory.php?id={$forum_result['category_id']}", 'name' => $category_name],
    ['url' => "viewforum.php?id={$_GET['id']}", 'name' => $forum_result['name']]
];

$navigation = construct_navigation($links);
// End Navigation
// Main content
$content =
    '<div id="actions">
            <input type="button" id="new-topic" value="New Topic">
        </div>
        <table>';
$stmt = $pdo->prepare("SELECT id, title FROM {$table_prefix}_topics WHERE forum_id = ?");
$stmt->execute([$id]);
$content .= '<thead>';
$content .= '<th>Topic Title</th>';
$content .= '<th>Replies</th>';
$content .= '</thead>';
$content .= '<tbody>';
foreach ($stmt as $row) {
    $content .= '<tr>';
    $posts_stmt = $pdo->prepare("SELECT id FROM {$table_prefix}_posts WHERE topic_id = ?");
    $posts_stmt->execute([$row['id']]);
    $content .= '<td><a href="viewtopic.php?id=' . $row['id'] . '">' . $row['title'] . '</a></td>';
    $content .= '<td>' . $posts_stmt->rowCount() . '</td>';
    $content .= '</tr>';
}
$content .= '</tbody></table>';
$script = 'viewforum.js';

$page = new Page($title, $navigation, $content, $script);
$page->write_html();