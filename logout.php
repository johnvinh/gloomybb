<?php
require_once 'classes/Page.php';
require_once 'inc/helpers.php';

session_start();
session_unset();
session_destroy();

$navigation = construct_navigation([
    ['url' => 'index.php', 'name' => 'Index'],
    ['url' => 'login.php', 'name' => 'Login']
]);
$page = new Page("Logout", $navigation, "<p>Logout successful!</p>");
$page->write_html();
header("refresh:2;url=index.php");