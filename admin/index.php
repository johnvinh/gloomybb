<?php
require_once dirname(__FILE__, 2) . '/inc/config.php';
require_once FORUM_ROOT . 'inc/dbconnect.php';
require_once FORUM_ROOT . 'inc/helpers.php';
require_once FORUM_ROOT . 'classes/Page.php';

$navigation = construct_navigation([
    ['url' => '../index.php', 'name' => 'Index']
]);
// Make sure the user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . '../login.php');
}
// User needs to be the admin
else if ($_SESSION['user_id'] !== 1) {
    $page = new Page("Not Authorized", $navigation, "You are not authorized to view this page.");
    $page->write_html();
    die();
}

$content = '<form action="addcategory.php" method="post">
<div>
<label for="category-name">Category Name</label>
<input type="text" id="category-name" name="category-name">
</div>
<input type="submit" name="submitting" value="Add Category">
</form>
';
$page = new Page("Admin Control Panel", $navigation, $content);
$page->write_html();