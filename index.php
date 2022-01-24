<?php
require_once 'inc/config.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo FORUM_NAME; ?> - Index</title>
    <meta charset="utf-8">
</head>
<body>
<div id="content">
    <header>
        <h1><?php echo FORUM_NAME; ?></h1>
    </header>
    <main>
        <?php
        require_once 'inc/dbconnect.php';
        $pdo = get_pdo();
        $table_prefix = TABLE_PREFIX;

        $stmt = $pdo->prepare("SELECT * FROM {$table_prefix}_categories");
        $stmt->execute();
        // Each category should have its own block of forums
        foreach ($stmt as $row) {
            $category_id = $row['id'];
            $category_name = $row['name'];
            echo '<div class="category-block">' . "\n";
            // Since this is tabular data, we can use a table instead of CSS grid for lining things up
            echo '<table>' . "\n";
            // Headings
            echo '<thead><tr>';
            echo '<th>' . $category_name . '</th>';
            echo '<th>Topics</th>';
            echo '<th>Posts</th>';
            echo '</tr></thead>';
            $forum_stmt = $pdo->prepare("SELECT * FROM {$table_prefix}_forums WHERE category_id = ?");
            $forum_stmt->execute([$category_id]);
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
        }
        // Close DB connection
        $pdo = null;
        ?>
    </main>
</div>
</body>
</html>