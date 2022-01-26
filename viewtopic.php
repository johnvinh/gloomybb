<?php
session_start();
require_once 'inc/config.php';
require_once 'inc/dbconnect.php';

// Topic ID must be specified in URL
if (!isset($_GET['id'])) {
    echo 'Missing topic ID!';
    die();
}

$table_prefix = TABLE_PREFIX;
$pdo = get_pdo();
$stmt = $pdo->prepare("SELECT * FROM {$table_prefix}_topics WHERE id = ?");
$stmt->execute([$_GET['id']]);
$result = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo FORUM_NAME . ' - ' . "{$result['title']}"; ?></title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div id="content">
    <header>
        <h1><?php echo FORUM_NAME . ' - ' . "{$result['title']}"; ?></h1>
        <nav>
            <a href="index.php">Index</a> ->
            <?php
            $category_stmt = $pdo->prepare("SELECT {$table_prefix}_categories.name, {$table_prefix}_categories.id FROM {$table_prefix}_categories INNER JOIN
    {$table_prefix}_forums ON {$table_prefix}_forums.category_id = {$table_prefix}_categories.id INNER JOIN
    {$table_prefix}_topics ON {$table_prefix}_forums.id = {$table_prefix}_topics.forum_id WHERE {$table_prefix}_topics.id = ?");
            $category_stmt->execute([$_GET['id']]);
            $category_results = $category_stmt->fetch();
            $forum_stmt = $pdo->prepare("SELECT name FROM {$table_prefix}_forums WHERE id = ?");
            $forum_stmt->execute([$result['forum_id']]);
            $forum_result = $forum_stmt->fetch();

            echo '<a href="viewcategory.php?id=' . $category_results['id'] . '">' . $category_results['name'] . '</a>' . '->';
            echo '<a href="viewforum.php?id=' . $result['forum_id'] . '">' . $forum_result['name'] . '</a>' . '->';
            echo '<a href="viewtopic.php?id=' . $_GET['id'] . '">' . $result['title'] . '</a>';
            ?>
        </nav>
    </header>
    <main>
        <input type="button" id="new-reply" value="New Reply">
        <?php
        // Topic contents
        echo '<div class="post">';

        // User details
        echo '<dl>';
        echo '<dt>Username</dt>';
        $user_stmt = $pdo->prepare("SELECT username FROM {$table_prefix}_users WHERE id = ?");
        $user_stmt->execute([$result['user_id']]);
        echo '<dd>' . $user_stmt->fetch()['username'] . '</dd>';
        echo '</dl>';
        // Post details
        echo '<div>';
        echo $result['content'];
        echo '</div>';

        // End topic contents
        echo '</div>';

        // Posts
        $posts_stmt = $pdo->prepare("SELECT * FROM {$table_prefix}_posts WHERE topic_id = ?");
        $posts_stmt->execute([$_GET['id']]);
        foreach ($posts_stmt as $post) {
            echo '<div class="post">';
            // User details
            echo '<dl>';
            echo '<dt>Username</dt>';
            $user_stmt = $pdo->prepare("SELECT username FROM {$table_prefix}_users WHERE id = ?");
            $user_stmt->execute([$post['user_id']]);
            echo '<dd>' . $user_stmt->fetch()['username'] . '</dd>';
            echo '</dl>';
            // Post details
            echo '<div>';
            echo $post['content'];
            echo '</div>';

            echo '</div>';
        }
        $pdo = null;
        ?>
    </main>
</div>
<script type="text/javascript">
    function getTopicId()
    {
        const regex = new RegExp('^.+?\\?id=(.+)$');
        return regex.exec(window.location.href)[1];
    }

    document.querySelector('#new-reply').addEventListener('click', (e) =>
    {
        window.location.href = `newreply.php?id=${getTopicId()}`;
    });
</script>
</body>
</html>
