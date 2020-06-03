<?php
session_start();
function createGame()
{
   //Create array with random chars
    $arraySopa = [];
    for ($i = 0; $i < $_SESSION["rows"]; $i++) {
        for ($j = 0; $j < $_SESSION["cols"]; $j++) {
            $randChar = chr(rand(65, 90));
            $arraySopa[$i][$j] = "<td>$randChar</td>";
        }
    }

    //Select n($_SESSION["words"]) random words
    $fileData = './word.txt';          //File path
    $arrayAllWords = explode("\n", trim(file_get_contents($fileData)));

    $randomWords = [];
    while (count($randomWords) < $_SESSION["words"]) {
        $randomNum = rand(0, count($arrayAllWords) - 1);
        if (!in_array(strtoupper($arrayAllWords[$randomNum]), $randomWords)) {
            array_push($randomWords, strtoupper($arrayAllWords[$randomNum]));
        }
    }

    //Add words to the game
    for ($indexWord = 0; $indexWord < count($randomWords); $indexWord++) {
        $vertical = rand(0, 1);     //1 vertival 0 horizontal
        if ($vertical) {
            $fila = rand(0, $_SESSION["rows"] - strlen($randomWords[$indexWord])-1);
            $columna = rand(0, $_SESSION["cols"]-1);
            for ($i = 0; $i < strlen($randomWords[$indexWord])-1; $i++) {
                $letra = substr($randomWords[$indexWord], $i, 1);
                $arraySopa[$fila + $i][$columna] = "<td class='marcar'>" . $letra . "</td>";
            }
        } else {
            $fila = rand(0, $_SESSION["rows"]-1);
            $columna = rand(0, $_SESSION["cols"] - strlen($randomWords[$indexWord]));
            for ($i = 0; $i < strlen($randomWords[$indexWord])-1; $i++) {
                $letra = substr($randomWords[$indexWord], $i, 1);
                $arraySopa[$fila][$columna + $i] = "<td class='marcar'>" . $letra . "</td>";
            }
        }
    }

    return $arraySopa;
}

if (!isset($_SESSION["game"]) or is_null($_SESSION["game"])){
    if (!isset($_GET["player"],$_GET["rows"],$_GET["cols"],$_GET["words"])){
        header('Location:./index.php');
        die();
    }
    $_SESSION["player"] =  $_GET["player"];
    $_SESSION["rows"] =  $_GET["rows"];
    $_SESSION["cols"] =  $_GET["cols"];
    $_SESSION["words"] =  $_GET["words"];
    $_SESSION["game"] = createGame();
}
$arraySopa = $_SESSION["game"];
?>

<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <style>
        * {
            text-transform: uppercase;
        }

        table {
            border: 1px solid black;
            border-collapse: collapse;
            table-layout: fixed;
            text-align: center;
        }

        td {
            border: 1px solid gray;
            height: 60px;
            width: 60px;
        }

        .marcar {
            background: red;
        }
    </style>
</head>

    <body>
    
        <?php
        echo "<table>";
        foreach ($arraySopa as $row) {
            echo "<tr>";
            foreach ($row as $vale) {
                echo $vale;
            }
            echo "</tr>";
        }
        echo "</table>";
        ?>

        <form action="./index.php" method="get">
            <button type="submit">Salir</button>
        </form>
    </body>
</html>
