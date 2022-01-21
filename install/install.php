<?php
function exit_if_failure(PDOStatement $stmt)
{
    if (!$stmt->execute()) {
        echo 'Installation failed!';
        $GLOBALS["pdo"]->rollBack();
        die();
    }
}

// Ensure data got sent via form
if (empty($_POST)) {
    echo "Missing form information!";
    die();
}
else if (!isset($_POST['host'])) {
    echo "Missing host!";
    die();
}
else if (!isset($_POST['db-name'])) {
    echo "Missing DB name!";
    die();
}
else if (!isset($_POST['db-user'])) {
    echo "Missing DB user!";
    die();
}
else if (!isset($_POST['db-password'])) {
    echo "Missing DB user password!";
    die();
}
else if (!isset($_POST['table-prefix'])) {
    echo "Missing table prefix!";
    die();
}

// Connect to the database
$dsn = "mysql:host={$_POST['host']};dbname={$_POST['db-name']};charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];
$pdo = null;
try {
    $pdo = new PDO($dsn, $_POST['db-user'], $_POST['db-password'], $options);
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}

// Use a transaction so we don't have a partially completed operation
$pdo->beginTransaction();

// Create tables
$stmt = $pdo->prepare("CREATE TABLE `{$_POST['table-prefix']}_users` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL
)");
exit_if_failure($stmt);
$stmt = $pdo->prepare("CREATE TABLE `{$_POST['table-prefix']}_forums` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL
)");
exit_if_failure($stmt);
$stmt = $pdo->prepare("CREATE TABLE `{$_POST['table-prefix']}_topics` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `forum_id` int
)");
exit_if_failure($stmt);
$stmt = $pdo->prepare("CREATE TABLE `{$_POST['table-prefix']}_posts` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `content` text,
  `topic_id` int
)");
exit_if_failure($stmt);

// Add foreign keys
$stmt = $pdo->prepare("ALTER TABLE `{$_POST['table-prefix']}_topics` ADD FOREIGN KEY (`forum_id`) REFERENCES `{$_POST['table-prefix']}_forums` (`id`)");
exit_if_failure($stmt);

$pdo->commit();

// Close database connection
$pdo = null;