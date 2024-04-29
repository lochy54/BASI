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
select * from insegnamento where codicedocente=$1";
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
                        <a class="nav-link" href="gestP.php">Gestisci appello</a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5" style="width:100%;" >
        <h2>Benvenuto, <?php echo $nome; ?> <?php echo $cognome; ?> </h2>
    <div id="c1">
<form action='inesrtappello.php' method='POST'>




     
        <div class="card mb-3"  style="width:60%;">
            <div class="card-header">
                Crea appello
            </div>

            <div class='card-body'>

            <table class="table">
  <thead class="thead-dark">
    <tr>
      <th scope="col">Codice corso</th>
      <th scope="col">Codice insegnameto</th>
      <th scope="col">Nome</th>
      <th scope="col">Crediti</th>
      <th scope="col">Seleziona</th>
    </tr>
  </thead>
  <tbody>

            <?php
            
            
            while($row = pg_fetch_row($res)){


                $idcorso = $row[2];
                $idinsegnamento = $row[3];
                $nomei = $row[4];
                $crediti = $row[5];
                    print(" 
                  


                    <tr>
                    <td> $idcorso</td>
                    <td>$idinsegnamento</td>
                    <td>$nomei</td>
                    <td> $crediti</td>
                    <td>     <input class='form-check-input' type='radio' name='radeo' id='radeo' value=$idinsegnamento>
                    <label class='form-check-label' for='flexRadioDefault1'>SELEZIONA</label></td>
                
              </tr>


               ");   
             }
             
            ?>

</tbody>
</table>
</div>
        </div>

        </div>    

 <div class="card mb-3"  style="left:60%; top:18%;position:absolute;  ">
            <div class="card-header">
                Crea appello
            </div>
            <div class='card-body'>
            <p><strong>Data appello:</strong></p>
            <p> <input type="datetime-local" class="form-control" id="data" name="data"></p>
             <p><strong>Numero posti:</strong></p>
             <p> <input type="number" min="1" max="100" id="posti" class="form-control" name="posti"></p>
             <p><button type="submit" class="btn btn-primary">Crea appello</button></p>

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
