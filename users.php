<?php

if (isset($_POST["name"])) {
    $users = json_decode(file_get_contents("data/passwords.json"), true);
    foreach ($users as $pwd => $u)
        if ($u["name"] == $_POST["name"] && $_COOKIE["auth"] != $pwd)
            $user = $pwd;
    if (isset($_POST["create"]) && isset($_POST["password"]) && !isset($user))
        $users[hash("sha256", $_POST["password"])] = ["name" => htmlspecialchars($_POST["name"]), "level" => 0];
    else if (isset($user)) {
        if (isset($_POST["save"]) && isset($_POST["level"]) && ctype_digit("" . $_POST["level"]) && $_POST["level"] > -1 && $_POST["level"] < 4)
            $users[$user]["level"] = intval($_POST["level"]);
        if (isset($_POST["delete"]))
            unset($users[$user]);
    }
    file_put_contents("data/passwords.json", json_encode($users));
}

?>

<html lang="de">

<head>

    <meta charset="UTF-8">
    <title>Benutzer verwalten</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Fabian Dietrich">
    <meta name="description" content="Berühmte Zitate in unbekannten Kreisen">
    <meta name="robots" content="nofollow, noindex">
    <link rel="stylesheet" href="/style.css">

</head>

<body>

<h1>Benutzer verwalten</h1>

<form method="post">
    <input type="text" name="name" placeholder="Benutzername" required>
    <input type="password" name="password" placeholder="Passwort" required>
    <button type="submit" name="create" class="button">Benutzer hinzufügen</button>
</form>

<div id="users">
    <?php
    $users = json_decode(file_get_contents("data/passwords.json"), true);
    foreach ($users as $user)
        echo("<form method='post'><span>" . $user["name"] . "</span><input type='hidden' name='name' value='" . $user["name"] . "'><span class='right'><select name='level' class='button'><option value='0'" . ($user["level"] == 0 ? " selected" : "") . ">Gast</option><option value='1'" . ($user["level"] == 1 ? " selected" : "") . ">VIP</option><option value='2'" . ($user["level"] == 2 ? " selected" : "") . ">Autor</option><option value='3'" . ($user["level"] == 3 ? " selected" : "") . ">Admin</option></select><button type='submit' class='button' name='save'>✅</button><button type='submit' class='button' name='delete'>❌</button></span></form>");
    ?>
</div>

</body>

</html>

