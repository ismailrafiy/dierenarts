<?php

include 'functies.php';

$conn = maakConnectie();

$arrDier = maakArray($conn);
//print_r($arrDier);

$idCurrentDier = NULL;
if(isset($_GET['idCurrentDier'])){
    $idCurrentDier = $_GET['idCurrentDier'];
}

$actie = NULL;

if(isset($_GET['actie'])){
    $actie = $_GET['actie'];
    $_GET['actie'] = NULL;
}

if($idCurrentDier != NULL && $actie=="updateDier"){
    $sql = "UPDATE dieren, eigenaars, aandoeningen, behandelingen SET 
    naam = '{$_GET['naam']}', 
    naam_eigenaar = '{$_GET['eigenaar']}', 
    aandoening = '{$_GET['aandoening']}', 
    beschrijving_aandoening = '{$_GET['beschrijving']}',
    datum_behandeling =' {$_GET['datum']}',
    behandeling =' {$_GET['behandeling']}'
    WHERE id = $idCurrentDier";
    if ($conn->query($sql) === TRUE) {
      $arrDier = maakArray($conn);
    } else {
      echo "Error updating record: " . $conn->error;
    }
}elseif(isset($_GET['naam']) && $actie=="newDier"){
    $sql = "INSERT INTO dieren (naam) VALUES ('{$_GET['naam']}')
            INSERT INTO eigenaars (voor_achternaam) VALUES ('{$_GET['eigenaar']}')
            INSERT INTO aandoeningen (aandoening, beschrijving) VALUES ('{$_GET['aandoening']}', '{$_GET['beschrijving']}')
            INSERT INTO behandelingen (datum, behandeling) VALUES ('{$_GET['datum']}', '{$_GET['behandeling']}')";

    if ($conn->query($sql) === TRUE) {
      $idCurrentDier = $conn->insert_id;
      $arrDier = maakArray($conn);
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

?>
<!doctype html>
<html lang="nl">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/styles.css">

    <title>Dier</title>
  </head>
  <body>
    <form method="GET">
        <input type="hidden" name="actie" value="">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>DIER</h1>
                </div>
            </div>
            <hr>
            <?php print kiesDier($arrDier,$idCurrentDier); ?>
            <?php print formDier($arrDier,$idCurrentDier); ?>
            <?php print buttonBar($idCurrentDier) ?>
            
        </div>
    </form>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  </body>
</html>
