<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true)
{
	header("Location: index.html");
	exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout']))
{
	session_destroy();
	header("Location: index.html");
	exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$random_number = rand(10000, 99999);
	$_SESSION['random_number'] = $random_number;
	$_SESSION['expiry_time'] = time() + 86400; // 24 hours
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<title>Dashboard</title>
</head>
<body>
<h2>Welcome!</h2>
<form method="post">
<input type="submit" value="Generate Code">
</form>

<?php
if (isset($_SESSION['random_number']))
{
	$db = new SQLite3('sentry.db');
	$stmt = $db->prepare('INSERT INTO tokens (username,token,expire) VALUES(:username, :token, :expire)');
	$stmt->bindValue(':username', $_SESSION['username'], SQLITE3_TEXT);
	$stmt->bindValue(':token', $_SESSION['random_number'], SQLITE3_TEXT);
	$stmt->bindValue(':expire', $_SESSION['expiry_time'], SQLITE3_INTEGER);
	$stmt->execute();
	unset($db);
	echo "<p>Access Token: " . $_SESSION['random_number'] . "</p>";
	echo "<p>Valid Until: " . date("Y-m-d H:i:s", $_SESSION['expiry_time']) . "</p>";
}
?>
<br/>
<form method="post"> <input type="submit" name="logout" value="Logout"> </form>
</body>
</html>
