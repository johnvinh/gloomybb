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
    </header>
    <main>
        <?php
        // Topic contents
        echo '<div class="post">';

        // User details
        echo '<dl>';
        echo '<dt>Username</dt>';
        $user_stmt = $pdo->prepare("SELECT * FROM {$table_prefix}_users WHERE id = ?");
        $user_stmt->execute([$result['id']]);
        echo '<dd>' . $user_stmt->fetch()['username'] . '</dd>';
        echo '</dl>';
        // Post details
        echo '<div>';
        echo $result['content'];
        echo '</div>';

        // End topic contents
        echo '</div>';

        $pdo = null;
        ?>
    </main>
</div>
</body>
</html>
