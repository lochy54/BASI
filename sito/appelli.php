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
$id_corso = $_SESSION['id_corso'];


$conn=pg_connect("host=localhost user=postgres port=5432 dbname=ProgettoUni password=7865");

if (!$conn) {
    session_destroy();
    $Message = urlencode("Errore connessione");
  header("Location: index.php?Message=".$Message);
  exit;
}


$sql = "

select * from 
appelli inner join insegnamento ON insegnamento.idinsegnamento = appelli.idinsegnamento 
		inner join corso_di_laurea ON corso_di_laurea.idcorso = insegnamento.idcorso 
		where insegnamento.idcorso= $1 and appelli.dataappello>=CURRENT_DATE and  
		
		appelli.idappello not in(select idappello from provare where matricola= $2)
";
$res = pg_prepare($conn,"q1",$sql);
$res = pg_execute($conn, "q1", array($id_corso,$mat));





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
                        <a class="nav-link" href="studenti.php">Studente</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="carriera.php">Carriera</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="carriera_tot.php">Storico</a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h2>Benvenuto, <?php echo $nome; ?> <?php echo $cognome; ?> </h2>
<div id="c1">

    
        <div class="card mb-3">
            <div class="card-header">
                Appelli
            </div>
            <div class='card-body'>
            <table class="table">
  <thead class="thead-dark">
    <tr>
      <th scope="col">Codice appello</th>
      <th scope="col">Data appello</th>
      <th scope="col">Crediti</th>
      <th scope="col">Nome</th>
      <th scope="col">Iscriviti</th>
    </tr>
  </thead>
  <tbody>
            <?php
            
            
            while($row = pg_fetch_row($res)){


                $idappello = $row[0];
                $dataappello = $row[1];
                $idinsegnamento = $row[2];
                $posti = $row[3];
                $idpersona = $row[4];
                $codicedocente = $row[5];
                $idcorso = $row[6];
                $idinsegnamento_2 = $row[7];
                $nome = $row[8];
                $crediti = $row[9];
                $descrizione = $row[10];
                $durata = $row[11];
                $anno = $row[12];
                $idcorso_2 = $row[13];
                $durata_2 = $row[14];
                $descrizione_2 = $row[15];
                $nome_2 = $row[16];

                    print(" 
                    <form action='inscrizione.php' method='POST'>
                    <tr>
                        <td> $idappello</td>
                        <td>$dataappello</td>
                        <td>$crediti</td>
                        <td> $nome</td>
                        <td><input type='hidden' id='id_app' name='id_app' value=$idappello><button class='btn btn-primary'>Iscriviti</button></td>

                  </tr>
                  </form>
                ");   
             }
             
                
            
          
            ?>
  </tbody>
</table>


</div>
        </div>

    </div>


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
