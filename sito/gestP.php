<?php

if(isset($_GET['Message'])){
    echo '<script type="text/javascript">
    window.onload = function () { alert("',$_GET["Message"],'"); } 
    </script>';
}


session_start();
$mat=$_SESSION['mat'];

$nome= $_SESSION['nome'];
$cognome = $_SESSION['cognome'];


$conn=pg_connect("host=localhost user=postgres port=5432 dbname=ProgettoUni password=7865");

if (!$conn) {
    session_destroy();
    $Message = urlencode("Errore connessione");
  header("Location: index.php?Message=".$Message);
  exit;
}


$sql = "
select * from appelli inner join insegnamento ON insegnamento.idinsegnamento = appelli.idinsegnamento where codicedocente=$1";
$res = pg_prepare($conn,"q1",$sql);
$res = pg_execute($conn, "q1", array($mat));





if (!$res) {
    session_destroy();
    $Message = urlencode("Errorre db");
    header("Location: index.php?Message=".$Message);
  }
  

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Area Studente</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">

</head>
<body>

      <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="docenti.php">Docente</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="creaP.php">Crea appello</a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5" style="width:100%;" >
        <h2>Benvenuto, <?php echo $nome; ?> <?php echo $cognome; ?> </h2>
    <div id="c1">




      
            <?php
            
            $nae=1;
            while($row = pg_fetch_row($res)){
               $nae++;

                $idapp = $row[0];
                $data = $row[1];
                $idins = $row[8];
                $posti = $row[9];


             


                $sql1 = "select * from provare where idappello=$1";
                $res1 = pg_prepare($conn,strval($nae),$sql1);
                $res1 = pg_execute($conn, strval($nae), array($idapp));





if (!$res1) {
    session_destroy();
    $Message = urlencode("Errorre db");
    header("Location: index.php?Message=".$Message);
  }
  

                    

                    print(" 
                    <div class='card mb-3'  style='width:100%;'>

                    <div class='card-header'>
                    <form action='modappello.php' method='POST'>
                    
                    <strong>Codice appello: </strong> $idapp
                    <strong>Codice insegnameto: </strong> $data
                    <strong>Nome: </strong> $idins
                    <strong>Crediti: </strong> $posti
                </div>
                <div class='card-body'>
    
                    ");
                    
                    print("
                    <div class='container'>
    <table class='table'>
        <thead>
            <tr>

                <th>Matricola</th>
                <th>Voto</th>
                <th>Data</th>
                <th>Ritirato</th>
                <th>Applica</th>
            </tr>
        </thead>
        <tbody>");

        while($row1 = pg_fetch_row($res1)){

            
            $amtricola = $row1[2];
            $voto = $row1[3];
            $dataiscrizione = $row1[4];
            $ritirato = $row1[5];
            $rit = "";    

            if($ritirato=="t"){
                $rit="checked";
            }


        print("            <tr>
        <td>$amtricola</td>
        <input type='hidden' value=$amtricola name='matr'>
        <input type='hidden' value=$idapp name='id'>
        <td><input type='text' class='form-control' value='$voto' name='voto'></td>
        <td>$dataiscrizione</td>
        <td><input type='checkbox' ".$rit." name='rit' ></td>
        <td> <button type='submit' class='btn btn-primary'>Salva</button></td>

    </tr>");

        }

print("</tbody>
</table>
</div>

  </div>
        </div>
         </form>");
             }
             
            ?>


        </div>

        </div>    
    </form>


</div>

    <footer class="bg-dark text-light text-center py-3" style="bottom: 0%; width: 100%; position: absolute;">
        <div class="container">
            <div class="row">
                <div class="col-md">
                    <h5>Address:</h5>
                    <address>
                        Via dell'Università, 123
                        Città Universitaria
                    </address>
                </div>
        </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>     