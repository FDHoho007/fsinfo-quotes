<!DOCTYPE html>
<html lang="de">

<head>

    <meta charset="UTF-8">
    <title>Zitate</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Fabian Dietrich">
    <meta name="description" content="Berühmte Zitate in unbekannten Kreisen">
    <meta name="robots" content="nofollow, noindex">
    <link rel="stylesheet" href="/style.css">

</head>

<body>

<h1>Zitate</h1>
Berühmte Zitate in unbekannten Kreisen<br>
<?php global $level; if($level >= 2) { ?>
<a class="button" href="/admin.php">Zitat hinzufügen</a>
<?php } ?>
<ul id="quotes"></ul>

<script>

    function processQuotes(quotes) {
        let ul = document.getElementById("quotes");
        for (let quote of quotes) {
            let li = document.createElement("li");
            for (let s of quote) {
                let div = document.createElement("div");
                div.classList.add("statement");
                div.innerHTML = s.author + ": " + s.message;
                li.appendChild(div);
            }
            ul.appendChild(li);
        }
    }

    fetch("/quotes.json").then(r => r.json().then(quotes => {
        processQuotes(quotes);
        fetch("/quotes.exclusive.json").then(r => r.json().then(quotes => {
            processQuotes(quotes);
        })).catch(() => {});
    }));

</script>

</body>

</html>
