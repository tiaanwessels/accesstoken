<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$db = new SQLite3('sentry.db');
	$input_number = $_POST['number'];
	if (!is_numeric($input_number))
	{
		echo "<p style='color: red;'>NO</p>";
	}
	else
	{
		$stmt = $db->prepare('SELECT * FROM tokens a, users b WHERE a.token = :token AND a.expire > :now AND a.username=b.username');
		$stmt->bindValue(':token', $_POST['number'], SQLITE3_TEXT);
		$stmt->bindValue(':now', time(), SQLITE3_INTEGER);
		$result = $stmt->execute();
		if ($row = $result->fetchArray(SQLITE3_ASSOC))
		{
			$expire = $row['expire'];
			$username = $row['username'];
			$descr = $row['descr'];
			if ($expire < time())
			{
				echo "<p style='color: red;'>NO</p>";
			}
			else
			{
				echo "<p style='color: green;'>" . "YES " . $username . " (" . $descr . ") </p>";
				$stmt = $db->prepare('DELETE FROM tokens WHERE token = :token AND expire = :expire AND username = :username');
				$stmt->bindValue(':token', $_POST['number'], SQLITE3_TEXT);
				$stmt->bindValue(':expire', $expire, SQLITE3_INTEGER);
				$stmt->bindValue(':username', $username, SQLITE3_TEXT);
				$result = $stmt->execute();
				$stmt = $db->prepare('INSERT INTO audit (username,token,expire,entry) VALUES (:username, :token, :expire, :entry)');
				$stmt->bindValue(':username', $username, SQLITE3_TEXT);
				$stmt->bindValue(':token', $_POST['number'], SQLITE3_TEXT);
				$stmt->bindValue(':expire', $expire, SQLITE3_INTEGER);
				$stmt->bindValue(':entry', time(), SQLITE3_INTEGER);
				$result = $stmt->execute();
				$stmt = $db->prepare('DELETE FROM tokens WHERE expire < :now');
				$stmt->bindValue(':now', time(), SQLITE3_INTEGER);
				$result = $stmt->execute();
			}
		}
		else
		{
			echo "<p style='color: red;'>NO</p>";
		}
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Access Token</title>
</head>
<body>
    <h2>Verify Access Token</h2>
    <form method="post">
        <label for="number">Enter Number:</label>
        <input type="text" id="number" name="number" required><br><br>
        <input type="submit" value="Verify">
    </form>
</body>
</html>

