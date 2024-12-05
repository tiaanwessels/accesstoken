<?php
// Open the database
$db = new SQLite3('sentry.db');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$username = $_POST['username'];
	if ($username == 'admin')
	{
		echo "Forgetting the admin password is dealt with differently.";
		echo '<p/><a href=index.html>Login</a>';
		echo '<script> setTimeout(function() { window.location.href = "index.html"; }, 3000); </script>';
	}

	// Check if the username exists
	$stmt = $db->prepare('SELECT * FROM users WHERE username = :username');
	$stmt->bindValue(':username', $username, SQLITE3_TEXT);
	$result = $stmt->execute();
	if ($row = $result->fetchArray(SQLITE3_ASSOC))
	{
		// Generate a new password
		$new_password = substr(md5(rand()), 0, 8); // Generate a random 8-character password

		// Update the password in the database
		$stmt = $db->prepare('UPDATE users SET password = :new_password, change = :change WHERE username = :username');
		$stmt->bindValue(':username', $username, SQLITE3_TEXT);
		$stmt->bindValue(':new_password', password_hash($new_password, PASSWORD_DEFAULT), SQLITE3_TEXT);
		$stmt->bindValue(':change', time(), SQLITE3_INTEGER);
		$stmt->execute();

		$to = $username;
		$subject = "Password Reset";
		$message = "Your new temporary password is " . $new_password;
		$headers = "From: sender@example.com";
		if (mail($to, $subject, $message, $headers))
			echo "Your new password has been mailed to you";
		else
			echo "Failed to send email. Contact administrator for assistance.";
		echo '<p/><a href=index.html>Login</a>';
	}
	else
	{
		echo "Username not found.";
		echo '<p/><a href=index.html>Login</a>';
		echo '<script> setTimeout(function() { window.location.href = "index.html"; }, 3000); </script>';
	}
}
else
{
	header("Location: index.html");
}
?>

