<?php

function maakConnectie(){
        //connectie met databank
        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "dierenarts";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $database);

        // Check connection
        if ($conn -> connect_error) {
            die("Connection failed: " . $conn -> connect_error);
        }
        return $conn;
    }
	//Array van dier maken
    function maakArray($conn){
        //data selecteren
        $sql = "SELECT * FROM dieren";
        $result = $conn->query($sql);
        $arrDier = array();
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $arrDier[$row["id"]]['naam'] = $row["naam"];
                $sqlEigenaar = "SELECT 
                eigenaars.id as id_eigenaar,
                eigenaars.voor-achternaam as naam_eigenaar
                FROM dier_eigenaars
                INNER JOIN eigenaars 
                ON dier_eigenaar.id_eigenaar=eigenaars.id
                WHERE dier_eigenaar.id_dier =".$row["id"];

                $sqlAandoening = "SELECT 
                aandoeningen.id as id_aandoening,
                aandoeningen.aandoening as aandoening,
                aandoeningen.beschrijving as beschrijving_aandoening
                FROM dier_aandoening
                INNER JOIN aandoeningen
                ON dier_aandoening.id_aandoening=aandoeningen.id
                WHERE dier_aandoening.id_dier =".$row["id"];

                $sqlBehandeling = "SELECT
                behandelingen.id as id_behandeling,
                behandelingen.datum as datum_behandeling,
                behandelingen.behandeling as behandeling
                FROM aandoeningen
                INNER JOIN behandelingen
                ON aandoeningen.id=behandelingen.id_aandoening
                WHERE behandelingen.id_dier =".$row["id"];
                
                $rstEigenaar = $conn->query($sqlEigenaar);
                if ($rstEigenaar->num_rows > 0) {
                    while($rowEigenaar = $rstEigenaar->fetch_assoc()) {
                        $arrDier[$row["id"]]['eigenaars'][$rowEigenaar["id_eigenaar"]] = array(
                            "naam_eigenaar" => $rowEigenaar["naam_eigenaar"]);
                    }
                }

                $rstAandoening = $conn->query($sqlAandoening);
                if ($rstAandoening->num_rows > 0) {
                    while($rowAandoening = $rstAandoening->fetch_assoc()) {
                        $arrDier[$row["id"]]['aandoeningen'][$rowAandoening["id_aandoening"]] = array(
                            "aandoening" => $rowAandoening["aandoening"],
                            "beschrijving_aandoening" => $rowAandoening["beschrijving_aandoening"]);
                    }
                }

                $rstBehandeling = $conn->query($sqlBehandeling);
                if ($rstBehandeling->num_rows > 0) {
                    while($rowBehandeling = $rstBehandeling->fetch_assoc()) {
                        $arrDier[$row["id"]]['behandelingen'][$rowBehandeling["id_behandeling"]] = array(
                            "datum_behandeling" => $rowBehandeling["datum_behandeling"],
                            "behandeling" => $rowBehandeling["behandeling"]);
                    }
                }
            }
            
        } else {
            echo "0 results";
        }
        return $arrDier;

    }
?>