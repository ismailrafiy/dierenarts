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
	
	//Dropdown om mijn dier te selecteren
    function kiesDier($arrDier,$idCurrentDier){
        $returnString = "<div class='row'>
                <div class='col-12'>
                    <div class='form-group'>
                        <label for='idCurrentDier'>Kies een dier</label>
                        <select class='form-control' id='idCurrentDier' name='idCurrentDier' onchange='this.form.submit()'>
                            <option value=''>---NIEUW DIER---</option>";
        foreach($arrDier as $key => $value){
            $selected = NULL;
            if($key == $idCurrentDier){
                $selected = "SELECTED";
            }
              $returnString .="
                            <option value='$key' $selected >{$value['naam']}</option>";
        }
        $returnString .= "
                        </select>
                    </div>
                </div>
            </div>
            <hr>";
        return $returnString;
    }
	
	//Dromdown met de consult van mijn dier
    function formDier($arrDier,$idCurrentDier){
        $returnString = NULL;
        if($idCurrentDier != NULL){
            $returnString = PHP_EOL . "
            <div class='row'>
                <div class='col-12'>
                    <h2>Dier</h2>
                </div>
                <div class='col-6'>
                    <div class='form-group'>
                        <label for='naam'>naam</label>
                        <input type='text' class='form-control' id='naam' name='naam' value='{$arrDier[$idCurrentDier]['naam']}'>
                    </div>
                </div>
                <div class='col-12'>
                    <h2>Eigenaars</h2>
                </div>
                <div class='col-6'>
                    <div class='form-group'>";
                    foreach ($arrDier[$idCurrentDier]['eigenaars'] as $key => $value) {
                        $returnString .= "<label for='naam_eigenaar'>naam</label>
                        <input type='text' class='form-control' id='eigenaar' name='eigenaar' value='{$value['naam_eigenaar']}'>";
                    };
                $returnString .= "</div>
                </div>
                <div class='col-12'>
                    <h2>Aandoening</h2>
                </div>
                <div class='col-6'>
                    <div class='form-group'>";
                    foreach ($arrDier[$idCurrentDier]['aandoeningen'] as $key => $value) {
                        $returnString .= "<label for='aandoening'>aandoening</label>
                        <input type='text' class='form-control' id='aandoening' name='aandoening' value='{$value['aandoening']}'>
                        <label for='beschrijving_aandoening'>beschrijving</label>
                        <input type='text' class='form-control' id='beschrijving' name='beschrijving' value='{$value['beschrijving_aandoening']}'>";
                    };
                    $returnString .= "</div>
                </div>
                <div class='col-12'>
                    <h2>Behandeling</h2>
                </div>
                <div class='col-10'>
                    <div class='form-group'>";
                    foreach ($arrDier[$idCurrentDier]['behandelingen'] as $key => $value) {
                        $returnString .= "<label for='datum_behandeling'>datum</label>
                        <input type='date' class='form-control' id='datum' name='datum' value='{$value['datum_behandeling']}'>
                        <label for='behandeling'>behandeling</label>
                        <input type='text' class='form-control' id='behandeling' name='behandeling' value='{$value['behandeling']}'>";
                    };
                    $returnString .= "</div>
                </div>
            </div><hr>";
            
        }else{
            $returnString = PHP_EOL . "<div class='row'>
            <div class='col-12'>
                <h2>Dier</h2>
            </div>
            <div class='col-6'>
                <div class='form-group'>
                    <label for='naam'>naam</label>
                    <input type='text' class='form-control' id='naam' name='naam' value=''>
                </div>
            </div>
            <div class='col-12'>
                <h2>Eigenaar</h2>
            </div>
                <div class='col-6'>
                    <div class='form-group'>
                        <label for='naam_eigenaar'>naam</label>
                        <input type='text' class='form-control' id='eigenaar' name='eigenaar' value=''>
                    </div>
                </div>
            <div class='col-12'>
                <h2>Aandoening</h2>
            </div>
                <div class='col-6'>
                    <div class='form-group'>
                        <label for='aandoening'>aandoening</label>
                        <input type='text' class='form-control' id='aandoening' name='aandoening' value=''>
                        <label for='beschrijving_aandoening'>beschrijving</label>
                        <input type='text' class='form-control' id='beschrijving' name='beschrijving' value=''>
                    </div>
                </div>
            <div class='col-12'>
                <h2>Behandeling</h2>
            </div>
                <div class='col-10'>
                    <div class='form-group'>
                    <label for='datum_behandeling'>datum</label>
                        <input type='date' class='form-control' id='datum' name='datum' value=''>
                        <label for='behandeling'>behandeling</label>
                        <input type='text' class='form-control' id='behandeling' name='behandeling' value=''>
                    </div>
                </div>
        </div><hr>";
        }
        return $returnString;
    }
?>