<?php
session_start();
require_once 'inc/config.php';

// Check if submit button was clicked
if (isset($_POST['newtopic']) && $_POST['newtopic'] === "Post") {
    // Make sure topic has a title
    if (empty($_POST['topic-name'])) {
        echo 'Missing topic name!';
        die();
    }
    // Make sure topic has a body
    else if (empty($_POST['topic-content'])) {
        echo 'Missing topic content!';
        die();
    }

    // Create the topic in the DB
    require_once 'inc/dbconnect.php';
    $table_prefix = TABLE_PREFIX;
    $pdo = get_pdo();
    $stmt = $pdo->prepare("INSERT INTO {$table_prefix}_topics (title, content, forum_id, user_id) VALUES
(?, ?, ?, ?)");
    if ($stmt->execute([$_POST['topic-name'], $_POST['topic-content'], $_POST['forum_id'], $_SESSION['user_id']])) {
        $new_topic_id =  $pdo->lastInsertId();
        $pdo->commit();
        header("Location: viewtopic.php?id=${new_topic_id}");
        die();
    }
    else {
        echo 'Failed to create topic!';
        die();
    }
}

// Ensure user is logged in before continuing
if (!isset($_SESSION['user_id'])) {
    echo 'You need to login first!';
    header('refresh:2;url=login.php');
    die();
}
// Ensure there's a forum id
else if (!isset($_GET['forum_id'])) {
    echo 'No forum specified!';
    header('refresh:2;url=index.php');
    die();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo  FORUM_NAME; ?> - New Topic</title>
</head>
<body>
<div id="content">
    <header>
        <h1><?php echo  FORUM_NAME; ?> - New Topic</h1>
    </header>
    <main>
        <form action="newtopic.php" method="post">
            <div>
                <label for="topic-name">Topic Name</label>
                <input type="text" id="topic-name" name="topic-name">
            </div>
            <div>
                <label for="topic-content">Message</label>
               <textarea id="topic-content" name="topic-content"></textarea>
            </div>
            <input type="hidden" value="<?php echo $_GET['forum_id']; ?>" name="forum_id">
            <input type="submit" name="newtopic" value="Post">
        </form>
    </main>
</div>
</body>
</html>