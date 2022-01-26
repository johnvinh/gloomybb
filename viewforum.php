<?php
require_once 'inc/config.php';
require_once 'inc/dbconnect.php';

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo FORUM_NAME . ' - ' . $forum_result['name']; ?></title>
    <meta charset="utf-8">
</head>
<body>
<div id="content">
    <header>
        <h1><?php echo FORUM_NAME . ' - ' . $forum_result['name']; ?></h1>
        <nav>
            <a href="index.php">Index</a> ->
            <?php
            $category_stmt = $pdo->prepare("SELECT name FROM {$table_prefix}_categories WHERE id = ?");
            $category_stmt->execute([$forum_result['category_id']]);
            $category_name = $category_stmt->fetch()['name'];

            echo '<a href="viewcategory.php?id=' . $forum_result['category_id'] . '">' . $category_name . '</a>' . '->';
            echo '<a href="viewforum.php?id=' . $_GET['id'] . '">' . $forum_result['name'] . '</a>';
            ?>
        </nav>
    </header>
    <main>
        <div id="actions">
            <input type="button" id="new-topic" value="New Topic">
        </div>
        <table>
            <?php
            $stmt = $pdo->prepare("SELECT id, title FROM {$table_prefix}_topics WHERE forum_id = ?");
            $stmt->execute([$id]);
            echo '<thead>';
            echo '<th>Topic Title</th>';
            echo '<th>Replies</th>';
            echo '</thead>';
            echo '<tbody>';
            foreach ($stmt as $row) {
                echo '<tr>';
                $posts_stmt = $pdo->prepare("SELECT id FROM {$table_prefix}_posts WHERE topic_id = ?");
                $posts_stmt->execute([$row['id']]);
                echo '<td><a href="viewtopic.php?id=' . $row['id'] . '">' . $row['title'] . '</a></td>';
                echo '<td>' . $posts_stmt->rowCount() . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            ?>
        </table>
    </main>
</div>
</body>
<script type="text/javascript">
    function getForumId()
    {
        const regex = new RegExp('^.+?\\?id=(.+)$');
        return regex.exec(window.location.href)[1];
    }

    document.querySelector('#new-topic').addEventListener('click', (e) =>
    {
        window.location.href = `newtopic.php?forum_id=${getForumId()}`;
    });
</script>
</html>