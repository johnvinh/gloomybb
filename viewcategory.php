<?php
require_once 'inc/config.php';
require_once 'inc/dbconnect.php';
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo FORUM_NAME . ' - ' . $result['name']; ?></title>
</head>
<body>
<div id="content">
    <header>
        <h1><?php echo FORUM_NAME . ' - ' . $result['name']; ?></h1>
        <nav>
            <a href="index.php">Index</a> ->
            <?php
            echo '<a href="viewcategory.php?id=' . $_GET['id'] . '">' . $result['name'] . '</a>';
            ?>
        </nav>
    </header>
    <main>
        <?php
        echo '<div class="category-block">' . "\n";
        // Since this is tabular data, we can use a table instead of CSS grid for lining things up
        echo '<table>' . "\n";
        // Headings
        echo '<thead><tr>';
        echo '<th>' . $result['name'] . '</th>';
        echo '<th>Topics</th>';
        echo '<th>Posts</th>';
        echo '</tr></thead>';
        $forum_stmt = $pdo->prepare("SELECT * FROM {$table_prefix}_forums WHERE category_id = ?");
        $forum_stmt->execute([$_GET['id']]);
        echo '<tbody>';
        foreach ($forum_stmt as $forum_row) {
            $num_topics = $pdo->prepare("SELECT * FROM {$table_prefix}_topics WHERE forum_id = ?");
            $num_topics->execute([$forum_row['id']]);
            $num_posts = $pdo->prepare("SELECT 123_posts.id FROM 123_posts INNER JOIN 123_topics
    ON 123_posts.topic_id = 123_topics.id INNER JOIN 123_forums ON 123_topics.forum_id = 123_forums.id WHERE 123_topics.forum_id = ?");
            $num_posts->execute([$forum_row['id']]);
            echo '<tr>';
            echo '<td><a href="viewforum.php?id=' . $forum_row['id'] . '">' . $forum_row['name'] . '</a></td>';
            echo '<td>' . $num_topics->rowCount() . '</td>';
            echo '<td>' . $num_posts->rowCount() . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>' . "\n";
        echo '</div>' . "\n";
        ?>
    </main>
</div>
</body>
</html>
