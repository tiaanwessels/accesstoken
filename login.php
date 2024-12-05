<?php

session_start();

// so sessions expire
/*
ini_set('session.gc_maxlifetime', 1800);
ini_set('session.cookie_lifetime', 1800);
session_regenerate_id(true);
*/

$db = new SQLite3('sentry.db');
$db->exec('CREATE TABLE IF NOT EXISTS users (username TEXT PRIMARY KEY, password TEXT, change INTEGER, descr TEXT)');
$db->exec('CREATE TABLE IF NOT EXISTS tokens (username TEXT, token TEXT, expire INTEGER)');
$db->exec('CREATE TABLE IF NOT EXISTS audit (username TEXT, token TEXT, expire INTEGER, entry INTEGER)');

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$username = $_POST['username'];
	$password = $_POST['password'];

	$stmt = $db->prepare('SELECT * FROM users WHERE username = :username');
	$stmt->bindValue(':username', $username, SQLITE3_TEXT);
	$result = $stmt->execute();
	if ($row = $result->fetchArray(SQLITE3_ASSOC))
	{
		if ($password == $row['password'] || password_verify($password, $row['password']))
		{
			$_SESSION['loggedin'] = true;
			$_SESSION['username'] = $username;
			if ($username == 'admin')
			{
				$_SESSION['admin'] = true;
				header("Location: crud.php");
			}
			else
			{
				$_SESSION['admin'] = false;
				if ($row['change'] < time())
					header("Location: change.html");
				else
					header("Location: dashboard.php");
			}
		}
		else
		{
			echo "Invalid username or password (1).";
			echo '<script> setTimeout(function() { window.location.href = "index.html"; }, 3000); </script>';
		}
	}
	else
	{
		echo "Invalid username or password (2).";
		echo '<script> setTimeout(function() { window.location.href = "index.html"; }, 3000); </script>';
	}
}

unset($db);

?>
