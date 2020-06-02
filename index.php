<?php
        session_start();
        session_destroy();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Menu</title>
</head>

<body>
    <form action="./game.php" method='get'>
        Player:<input type="text" name="player" required minlength="2" maxlength="14"><br>
        Rows:<input type="number" name="rows" required min="8" max="20"><br>
        Columns:<input type="number" name="cols" required min="8" max="20"><br>
        Words:<input type="number" name="words"  min="1" max="10"><br>
        <input type="submit">
    </form>
</body>

</html>