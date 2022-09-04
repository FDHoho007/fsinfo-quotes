<?php

const REALMS = [["/", "/index.php", "/quotes.json"], ["/quotes.exclusive.json"], ["/admin.php"], ["/users.php"]];

$realm = -1;
for ($i = 0; $i < sizeof(REALMS); $i++)
    foreach(REALMS[$i] as $quote)
	if(($quote == "/" && $_SERVER["REQUEST_URI"] == "/") || ($quote != "/" && str_starts_with($_SERVER["REQUEST_URI"], $quote)))
        	$realm = $i;

$level = -1;
if (isset($_COOKIE["auth"])) {
    $passwords = json_decode(file_get_contents("data/passwords.json"), true);
    if (array_key_exists($_COOKIE["auth"], $passwords))
        $level = $passwords[$_COOKIE["auth"]]["level"];
}

if ($level < $realm) { ?>

    <!DOCTYPE html>
    <html lang="de">

    <head>

        <meta charset="UTF-8">
        <title>Login</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Fabian Dietrich">
        <meta name="description" content="Geschützter Bereich">
        <meta name="robots" content="nofollow, noindex">
        <link rel="stylesheet" href="/style.css">

    </head>

    <body>

    <h1>Login</h1>
    <?php if (isset($_COOKIE["auth"])) { ?>
        <div class="error">
            Sie haben kein Zugriff auf dieses Dokument.
        </div>
    <?php } ?>
    Um auf diese Seite zuzugreifen ist ein Passwort erforderlich<br>
    <form onsubmit="sha256(document.getElementById('password').value).then(sha256 => { document.cookie = 'auth=' + sha256 + '; expires=Tue, 01 Jan 2030 00:00:00 UTC; path=/'; location.reload() }); return false;">
        <input type="password" id="password" placeholder="Passwort"><br>
        <button class="button" type="submit">Anmelden</button>
    </form>

    <script>

        async function sha256(message) {
            const hashBuffer = await crypto.subtle.digest('SHA-256', new TextEncoder().encode(message));
            return Array.from(new Uint8Array(hashBuffer)).map(b => b.toString(16).padStart(2, '0')).join('');
        }


    </script>

    </body>

    </html>


    <?php exit;
}
if (str_ends_with($_SERVER["REQUEST_URI"], ".json"))
    header("Content-Type: application/json");
