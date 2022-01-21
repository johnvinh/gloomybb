<!DOCTYPE html>
<html lang="en">
<head>
    <title>GloomyBB - Install</title>
    <meta charset="UTF-8">
</head>
<body>
<header>
    <h1>GloomyBB - Install</h1>
</header>
<main>
    <form action="install.php" method="post">
        <div>
            <label for="host">Host</label>
            <input type="text" id="host" name="host">
        </div>
        <div>
            <label for="db-name">DB Name</label>
            <input type="text" id="db-name" name="db-name">
        </div>
        <div>
            <label for="db-user">DB User</label>
            <input type="text" id="db-user" name="db-user">
        </div>
        <div>
            <label for="db-password">DB Password</label>
            <input type="text" id="db-password" name="db-password">
        </div>
        <div>
            <label for="table-prefix">Table Prefix</label>
            <input type="text" id="table-prefix" name="table-prefix">
        </div>
        <input type="submit" value="Install!">
    </form>
</main>
</body>
</html>