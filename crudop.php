<?php

// users add/mod/del

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit;
}
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: login.html");
    exit;
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout']))
{
	session_destroy();
	header("Location: index.html");
	exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	// Open the database
	$db = new SQLite3('sentry.db');

	$action = $_POST['action'];

	if ($action == "Create")
	{
		$username = $_POST['username'];
		$password = $_POST['password'];
		if (strlen($password) < 6)
		{
			echo "Password too short";
			echo '<script> setTimeout(function() { window.location.href = "crud.php"; }, 3000); </script>';
			exit;
		}
		$descr = $_POST['descr'];
		$stmt = $db->prepare('INSERT INTO users (username, password, change, descr) VALUES (:username, :password, :now, :descr)');
		$stmt->bindValue(':username', $username, SQLITE3_TEXT);
		$stmt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT), SQLITE3_TEXT);
		$stmt->bindValue(':now', time(), SQLITE3_INTEGER);
		$stmt->bindValue(':descr', $descr, SQLITE3_TEXT);
		$stmt->execute();
	}
	elseif ($action == "Update")
	{
		$username = $_POST['username'];
		$password = $_POST['password'];
		if (strlen($password) < 6)
		{
			echo "Password too short";
			echo '<script> setTimeout(function() { window.location.href = "crud.php"; }, 3000); </script>';
			exit;
		}
		$descr = $_POST['descr'];
		$stmt = $db->prepare('UPDATE users SET password = :password, change = :change, descr = :descr  WHERE username = :username');
		$stmt->bindValue(':username', $username, SQLITE3_TEXT);
		$stmt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT), SQLITE3_TEXT);
		$stmt->bindValue(':change', time(), SQLITE3_INTEGER);
		$stmt->bindValue(':descr', $descr, SQLITE3_TEXT);
		$stmt->execute();
	}
	elseif ($action == "Logout")
	{
		session_destroy();
		header("Location: index.html");
		exit;
	}
	else
	{
		$stmt = $db->prepare('DELETE FROM users WHERE username = :username');
		$stmt->bindValue(':username', $action, SQLITE3_TEXT);
		$stmt->execute();
		$stmt = $db->prepare('DELETE FROM tokens WHERE username = :username');
		$stmt->bindValue(':username', $action, SQLITE3_TEXT);
		$stmt->execute();
	}
}

header("Location: crud.php");

?>

