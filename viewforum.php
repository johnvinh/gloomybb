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
$subforum_name = $pdo->prepare("SELECT name FROM {$table_prefix}_forums WHERE id = ?");
$subforum_name->execute([$_GET['id']]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo FORUM_NAME . ' - ' . $subforum_name->fetch()['name']; ?></title>
    <meta charset="utf-8">
</head>
<body>
<div id="content">
    <header>
        <?php echo FORUM_NAME; ?>
    </header>
    <main>
        <div id="actions">
            <input type="button" id="new-topic" value="New Topic">
        </div>
        <table>
            <?php
            $stmt = $pdo->prepare("SELECT id, title FROM {$table_prefix}_topics WHERE id = ?");
            $stmt->execute([$id]);
            echo '<thead>';
            echo '<th>Topic Title</th>';
            echo '<th>Replies</th>';
            echo '</thead>';
            echo '<tbody>';
            foreach ($stmt as $row) {
                $posts_stmt = $pdo->prepare("SELECT id FROM {$table_prefix}_posts WHERE topic_id = ?");
                $posts_stmt->execute([$row['id']]);
                echo '<td>' . $row['title'] . '</td>';
                echo '<td>' . $posts_stmt->rowCount() . '</td>';
            }
            echo '</tbody>';
            ?>
        </table>
    </main>
</div>
</body>
</html>