<?php

require_once "HTTP.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && getallheaders()["content-type"] == "application/json") {
    $json = json_decode(file_get_contents("php://input"), true);
    $quote = [];
    if (array_key_exists("quote", $json) && array_key_exists("exclusive", $json) && is_array($json["quote"]))
        foreach ($json["quote"] as $statement)
            if (array_key_exists("author", $statement) && array_key_exists("message", $statement))
                $quote[] = ["author" => htmlspecialchars($statement["author"]), "message" => htmlspecialchars($statement["message"])];
    if (sizeof($quote) > 0) {
        $file = "quotes" . ($json["exclusive"] ? ".exclusive" : "") . ".json";
        $quotes = json_decode(file_get_contents($file), true);
        $quotes[] = $quote;
        file_put_contents($file, json_encode($quotes));
        $hooks = json_decode(file_get_contents("data/hooks.json"), true);
	if(!$json["exclusive"])
            foreach ($hooks as $hook)
                if ($hook["type"] == "https")
                    HTTP::post($hook["url"], json_encode(["status" => "add", "quotes" => [$quote]]), ["Content-Type" => "application/json"]);
                else if ($hook["type"] == "discord") {
                    if (sizeof($quote) > 1)
                        HTTP::post($hook["url"], json_encode(["content" => "--------------------", "username" => "quotes.fsinfo.lol"]), ["Accept: application/json", "Content-Type: application/json"]);
                    foreach ($quote as $statement)
                        HTTP::post($hook["url"], json_encode(["content" => $statement["message"], "username" => $statement["author"]]), ["Accept: application/json", "Content-Type: application/json"]);
                    if (sizeof($quote) > 1)
                        HTTP::post($hook["url"], json_encode(["content" => "--------------------", "username" => "quotes.fsinfo.lol"]), ["Accept: application/json", "Content-Type: application/json"]);
                }
        echo('{"success":true}');
    }
    echo('{"success":false}');
}

?>

<!DOCTYPE html>
<html lang="de">

<head>

    <meta charset="UTF-8">
    <title>Zitat hinzufügen</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Fabian Dietrich">
    <meta name="description" content="Berühmte Zitate in unbekannten Kreisen">
    <meta name="robots" content="nofollow, noindex">
    <link rel="stylesheet" href="/style.css">

</head>

<body>

<h1>Zitat hinzufügen</h1>

<form onsubmit="add(); return false;">

    <div class="statement">
        <input type="text" placeholder="Wer hat etwas gesagt?" onkeyup="updateStatements(this.parentElement)"
               list="names">
        <input type="text" placeholder="Was hat er/sie gesagt?" onkeyup="updateStatements(this.parentElement)">
    </div>

    <datalist id="names"></datalist>

    <input type="checkbox" id="exclusive"> <label for="exclusive">Nur für exklusive Mitglieder</label><br>

    <button type="submit" class="button">Hinzufügen</button>

</form>

<script>

    function add() {
        let quote = [];
        for (let s of document.getElementsByClassName("statement")) {
            let statement = [];
            for (let input of s.getElementsByTagName("input"))
                if (input.value !== "")
                    statement.push(input.value);
            if (statement.length === 2)
                quote.push({"author": statement[0], "message": statement[1]});
        }
        fetch("/admin.php", {
            headers: {
                "Content-Type": "application/json"
            },
            method: "POST",
            body: JSON.stringify({"quote": quote, "exclusive": document.getElementById("exclusive").checked})
        }).then(() => {
            location.href = "/";
        });
    }

    function updateStatements(e) {
        if (!e.hasAttribute("cloned"))
            for (let input of e.getElementsByTagName("input"))
                if (input.value !== "") {
                    let clone = e.cloneNode(true);
                    for (let input of clone.getElementsByTagName("input"))
                        input.value = "";
                    e.setAttribute("cloned", true);
                    document.getElementsByTagName("form")[0].insertBefore(clone, document.querySelector("input[type=checkbox]"));
                }
    }

    fetch("/quotes.json").then(r => r.json().then(quotes => {
        let names = [];
        for (let quote of quotes)
            for (let s of quote)
                if (!names.includes(s.author))
                    names.push(s.author);
        for (let name of names) {
            let option = document.createElement("option");
            option.value = name;
            document.getElementById("names").appendChild(option);
        }
    }));


</script>

</body>

</html>

