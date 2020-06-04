<?php
session_start();

function createGame($rows,$cols,$numWords)
{
    $arrayGame = createArrayFilledRandomly($rows,$cols);
    $randomWords = getRandomWordsFromFile($numWords);    
    $_SESSION["maxLetters"] = countLettersIn($randomWords);

    //Add words to the game board
    for ($indexWord = 0; $indexWord < count($randomWords); $indexWord++) {
        $vertical = rand(0, 1);     //1 vertival 0 horizontal
        if ($vertical) {
            $fila = rand(0, $rows - strlen($randomWords[$indexWord])-1);
            $columna = rand(0, $cols-1);
            for ($i = 0; $i < strlen($randomWords[$indexWord]); $i++) {
                $letra = substr($randomWords[$indexWord], $i, 1);
                $nextFila = $fila + $i;
                $arrayGame[$nextFila][$columna] = "<td class><button type='submit' name='cell' value=\"$nextFila-$columna\">" . $letra . "</button></td>";
            }
        } else {
            $fila = rand(0, $rows-1);
            $columna = rand(0, $cols - strlen($randomWords[$indexWord])); 
            for ($i = 0; $i < strlen($randomWords[$indexWord]); $i++) {
                $letra = substr($randomWords[$indexWord], $i, 1);
                $nextCol = $columna + $i;
                $arrayGame[$fila][$nextCol] = "<td class><button type='submit' name='cell' value=\"$fila-$nextCol\">" . $letra . "</button></td>";
            }
        }
    }
    return $arrayGame;
}

function createArrayFilledRandomly($rows,$cols)
{
    $array = [];
    for ($i = 0; $i < $rows; $i++) {
        for ($j = 0; $j < $cols; $j++) {
            $randChar = chr(rand(65, 90));
            $array[$i][$j] = "<td><button type='submit' name='cell'>$randChar</button></td>";
        }
    }
    return $array;
}

function getRandomWordsFromFile($number)
{
    $fileData = './word.txt';          //File path
    $arrayAllWords = explode("\n", trim(file_get_contents($fileData)));

    $randomWords = [];
    while (count($randomWords) < $number) {
        $randomNum = rand(0, count($arrayAllWords) - 1);
        if (!in_array(strtoupper($arrayAllWords[$randomNum]), $randomWords)) {
            array_push($randomWords, trim(strtoupper($arrayAllWords[$randomNum])));
        }
    }
    return $randomWords;
}

function countLettersIn($arrayWords)
{
    $letters = 0;
    foreach ($arrayWords as  $word) {
        $letters += strlen($word);
    }
    return $letters;
}

function removeButton($htmlString)
{
    // $htmlString = <td class='correcto'><button>A</button></td>
    $htmlString = preg_replace('/<button [^>]*>/',"",$htmlString);  //<td class='correcto'>A</button></td>
    $htmlString = preg_replace('/<\/button>/',"",$htmlString);      //<td class='correcto'>A</td>
    return $htmlString; 
}

function markAsCorrect($cell)
{
    //Take the value from cell
    $i = explode("-",$cell)[0];
    $j = explode("-",$cell)[1];

    //Add css class to see that is correct
    $aux = preg_replace('/class/',"class='correct'",$_SESSION["game"][$i][$j]);
    //Remove the button
    $aux = removeButton($aux);
    $_SESSION["game"][$i][$j] = $aux;
}

function checkWin()     
{
    if($_SESSION["hits"] == $_SESSION["maxLetters"]){
        saveInRanking();
        echo "<h1>Felicidades ganaste " . $_SESSION['player']."</h1>";
        echo '<form action="./index.php" method="get"><button type="submit">New Game</button></form>';
        die();
    }
}

function saveInRanking()
{
    $file = fopen("ranking.txt", "a");
    $points = ($_SESSION["hits"]*5) -$_SESSION["tries"];
    $record = $_SESSION['player'] . ";" . $points . "\n";
    fwrite($file, $record);                                 //save name and points in the ranking 
    fclose($file);
}

function render($array)
{
    foreach ($array as $row) {
        echo "<tr>";
        foreach ($row as $value) {
            echo $value;
        }
        echo "</tr>";
    }
}

if (!isset($_SESSION["game"]) or is_null($_SESSION["game"])){   //When no game saved in sessions

    //Check if have all necessary variables
    if (!isset($_GET["player"],$_GET["rows"],$_GET["cols"],$_GET["words"])){
        header('Location:./index.php');
        die();
    }
    $_SESSION["player"] =  $_GET["player"];
    $_SESSION["rows"] =  $_GET["rows"];
    $_SESSION["cols"] =  $_GET["cols"];
    $_SESSION["words"] =  $_GET["words"];
    $_SESSION["tries"] =  0;
    $_SESSION["hits"] =  0;
    $_SESSION["game"] = createGame($_SESSION["rows"],$_SESSION["cols"],$_SESSION["words"] );

}else if (isset($_GET["cell"])) {   //When player clicks on a letter
    if (empty($_GET["cell"])) {
        $_SESSION["tries"]++;
    }else{
        $_SESSION["hits"]++;
        markAsCorrect($_GET["cell"]);
        checkWin();
    }
}
$arrayGame = $_SESSION["game"];
?>

<head>
    <meta charset="UTF-8">
    <title>Game</title>
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
        .correct {
            background: red;
        }
    </style>
</head>
    <body>
        <div class="tries">Tries <?php echo $_SESSION["tries"]; ?></div>
        <div class="hits">Hits <?php echo $_SESSION["hits"]; ?></div>
        <form action="./game.php" method="get">
            <table><?php render($arrayGame) ?></table>
        </form>
        <form action="./index.php" method="get">
            <button type="submit">Menu</button>
        </form>
    </body>
</html>
