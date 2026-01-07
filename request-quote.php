<?php
// Start PHP tag
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request a Quote</title>
</head>
<body>
    <h1>Request a Quote</h1>

    <form method="post" action="submit-quote.php">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="details">Details:</label>
        <textarea id="details" name="details" required></textarea>

        <button type="submit">Submit</button>
    </form>
</body>
</html>
