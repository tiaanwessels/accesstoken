
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
</head>
<body>
    <h2>User Management</h2>
    <form action="crudop.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <label for="descr">Description:</label>
        <input type="text" id="descr" name="descr" required><br><br>
        <input type="submit" name="action" value="Create">
        <input type="submit" name="action" value="Update">
    </form>

    <form action="crudop.php" method="post">
    <h2>User List</h2>
<?php
	// Display users
	$db = new SQLite3('sentry.db');
	$result = $db->query('SELECT * FROM users');
	echo "<ul>";
	while ($row = $result->fetchArray(SQLITE3_ASSOC))
	{
		echo '<li>';
		if ($row['username'] != 'admin')
		{
			echo '<button type="submit" name="action" value="' . $row['username'] . '">x</button>' . ' (' . $row['descr'] . ') ';
		}
		echo htmlspecialchars($row['username']);
		echo '</li>';
	}
	echo "</ul>";
?>
    </form>
<br/>
<form action="crudop.php" method="post"> <input type="submit" name="action" value="Logout"> </form>
</body>
</html>

