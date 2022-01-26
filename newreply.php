<?php
require_once 'inc/config.php';
require_once 'inc/dbconnect.php';

session_start();

// Check if submit button is clicked
if (isset($_POST['posting']) && $_POST['posting'] === 'Post!') {
    // Ensure message isn't empty
    if (empty($_POST['post-content'])) {
        echo 'Missing post message!';
        die();
    }

    // Insert post into DB
    $pdo = get_pdo();
    $table_prefix = TABLE_PREFIX;
    $stmt = $pdo->prepare("INSERT INTO {$table_prefix}_posts (content, topic_id, user_id) VALUES (?, ?, ?)");
    if ($stmt->execute([$_POST['post-content'], $_POST['topic_id'], $_SESSION['user_id']])) {
        echo 'Post successful!';
        $new_post_id = $pdo->lastInsertId();
        // Redirect back to the topic
        header("Location: viewtopic.php?id={$_POST['topic_id']}#post{$new_post_id}");
        $pdo->commit();
    }
    else
        $pdo->rollback();
    // Close DB connection
    $pdo = null;
    die();
}

// Require a user to be logged in
if (!isset($_SESSION['user_id'])) {
    echo 'You need to be logged in!';
//    header('refresh:2;url=login.php');
    die();
}
// Ensure topic id is specified
else if (!isset($_GET['id'])) {
    echo 'Missing topic ID!';
    die();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo FORUM_NAME; ?> - New Reply</title>
</head>
<body>
<div id="content">
    <header>
        <h1><?php echo FORUM_NAME; ?> - New Reply</h1>
    </header>
    <main>
        <form action="newreply.php" method="post">
            <div>
                <label for="post-content">Message</label>
                <textarea id="post-content" name="post-content"></textarea>
            </div>
            <input type="hidden" name="topic_id" value="<?php echo $_GET['id']; ?>">
            <input type="submit" name="posting" value="Post!">
        </form>
    </main>
</div>
</body>
</html>