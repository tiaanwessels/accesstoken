<?php

// password change

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit;
}

// Open the database
$db = new SQLite3('sentry.db');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$username = $_SESSION['username'];
	$current_password = $_POST['current_password'];
	$new_password = $_POST['new_password'];
	if (strlen($new_password) < 6)
	{
		echo "Password too short";
		echo '<script> setTimeout(function() { window.location.href = "change.php"; }, 3000); </script>';
		exit;
	}

	// Verify current password
	$stmt = $db->prepare('SELECT * FROM users WHERE username = :username');
	$stmt->bindValue(':username', $username, SQLITE3_TEXT);
	$result = $stmt->execute();
	if ($row = $result->fetchArray(SQLITE3_ASSOC))
	{
		if (password_verify($current_password, $row['password']))
		{
			$hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
			$stmt = $db->prepare('UPDATE users SET password = :new_password, change = :change WHERE username = :username');
			$stmt->bindValue(':username', $username, SQLITE3_TEXT);
			$stmt->bindValue(':new_password', $hashed_new_password, SQLITE3_TEXT);
			$till = time()+365*60*60*24;
			$stmt->bindValue(':change', $till, SQLITE3_INTEGER);
			$stmt->execute();
			echo "Password changed successfully! Valid till " . date('r', $till);
			echo '<script> setTimeout(function() { window.location.href = "index.html"; }, 5000); </script>';
		}
		else
		{
			echo "Current password is incorrect (1).";
			echo '<script> setTimeout(function() { window.location.href = "index.html"; }, 3000); </script>';
		}
	}
	else
	{
		// actually no such user but we won't give the hacker any tips
		echo "Current password is incorrect (2).";
		echo '<script> setTimeout(function() { window.location.href = "index.html"; }, 3000); </script>';
	}
}

unset($db);

?>

