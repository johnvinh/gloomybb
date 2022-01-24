<?php
function exit_if_failure(PDOStatement $stmt)
{
    if (!$stmt->execute()) {
        echo 'Installation failed!';
//        $GLOBALS["pdo"]->rollBack();
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
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_AUTOCOMMIT => false
];

try {
    $pdo = new PDO($dsn, $_POST['db-user'], $_POST['db-password'], $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

try {

    // Create tables
    $stmt = $pdo->prepare("CREATE TABLE `{$_POST['table-prefix']}_users` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL
)");
    exit_if_failure($stmt);
    $stmt = $pdo->prepare("CREATE TABLE `{$_POST['table-prefix']}_categories` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL
)");
    exit_if_failure($stmt);
    $stmt = $pdo->prepare("CREATE TABLE `{$_POST['table-prefix']}_forums` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `category_id` int
)");
    exit_if_failure($stmt);
    $stmt = $pdo->prepare("CREATE TABLE `{$_POST['table-prefix']}_topics` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `content` text NOT NULL,
  `forum_id` int NOT NULL,
  `user_id` int NOT NULL
)");
    exit_if_failure($stmt);
    $stmt = $pdo->prepare("CREATE TABLE `{$_POST['table-prefix']}_posts` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `topic_id` int NOT NULL,
  `user_id` int NOT NULL
)");
    exit_if_failure($stmt);

// Add foreign keys
    $stmt = $pdo->prepare("ALTER TABLE `{$_POST['table-prefix']}_forums` ADD FOREIGN KEY (`category_id`) REFERENCES `{$_POST['table-prefix']}_categories` (`id`)");
    exit_if_failure($stmt);
    $stmt = $pdo->prepare("ALTER TABLE `{$_POST['table-prefix']}_topics` ADD FOREIGN KEY (`forum_id`) REFERENCES `{$_POST['table-prefix']}_forums` (`id`)");
    exit_if_failure($stmt);
    $stmt = $pdo->prepare("ALTER TABLE `{$_POST['table-prefix']}_topics` ADD FOREIGN KEY (`user_id`) REFERENCES `{$_POST['table-prefix']}_users` (`id`)");
    exit_if_failure($stmt);
    $stmt = $pdo->prepare("ALTER TABLE `{$_POST['table-prefix']}_posts` ADD FOREIGN KEY (`topic_id`) REFERENCES `{$_POST['table-prefix']}_topics` (`id`)");
    exit_if_failure($stmt);
    $stmt = $pdo->prepare("ALTER TABLE `{$_POST['table-prefix']}_posts` ADD FOREIGN KEY (`user_id`) REFERENCES `{$_POST['table-prefix']}_users` (`id`)");
    exit_if_failure($stmt);
    // Create admin user
    $pdo->beginTransaction();
    $password = password_hash($_POST['admin-password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO {$_POST['table-prefix']}_users (username, password) VALUES (?, ?)");
    $stmt->execute([$_POST['admin-username'], $password]);
    // Create a default category
    $stmt = $pdo->prepare("INSERT INTO {$_POST['table-prefix']}_categories (name) VALUES ('My First Category')");
    exit_if_failure($stmt);
    // Create a default forum
    $stmt = $pdo->prepare("INSERT INTO {$_POST['table-prefix']}_forums (name, category_id) VALUES ('My First Forum', 1)");
    exit_if_failure($stmt);
    $pdo->commit();
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

// Close database connection
$pdo = null;

// Write config file
$config_details = "<?php
// Forum Details
const FORUM_NAME = '{$_POST['forum-name']}';
// DB Details
const DB_HOST = '{$_POST['host']}';
const DB_NAME = '{$_POST['db-name']}';
const DB_USER = '{$_POST['db-user']}';
const DB_PASSWORD = '{$_POST['db-password']}';
const TABLE_PREFIX = '{$_POST['table-prefix']}';
";

$config = fopen("../inc/config.php", 'w');
fwrite($config, $config_details);
fclose($config);