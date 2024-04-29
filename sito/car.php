

<?php

if(isset($_GET['Message'])){
    echo '<script type="text/javascript">
    window.onload = function () { alert("',$_GET["Message"],'"); } 
    </script>';
}

$stu = $_POST['s'];

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

with t1 as(


    select * from provare inner join appelli on appelli.idappello = provare.idappello inner join insegnamento ON insegnamento.idinsegnamento = appelli.idinsegnamento
    
    union
    
    select * from provare_storico inner join appelli on appelli.idappello = provare_storico.idappello inner join insegnamento ON insegnamento.idinsegnamento = appelli.idinsegnamento
    
    
    )
    
    select * from t1 where matricola=$1
";
$res = pg_prepare($conn,"q1",$sql);
$res = pg_execute($conn, "q1", array($stu));





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
    <title>Cariera</title>
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
            
                    <li class="nav-item">
                        <a class="nav-link" href="segreteria.php">Segreteria</a>
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
                Carriera
            </div>
            <form action='inscrizione.php' method='POST'>
            <div class='card-body'>
            <table class="table">
  <thead class="thead-dark">
    <tr>
      <th scope="col">Nome</th>
      <th scope="col">ID insegnamento</th>
      <th scope="col">Voto</th>
      <th scope="col">Codice appello</th>
      <th scope="col">Ritirato</th>
      <th scope="col">Data</th>
    </tr>
  </thead>
  <tbody>
            <?php
            
            
            while($row = pg_fetch_assoc($res)){

                $voto = $row['voto'];
                $insegno = $row['idinsegnamento'];
                $nome= $row['nome'];
                $idapp= $row['idappello'];
                $ritirato= $row['ritirato'];
                $data= $row['dataappello'];
                
                print(" 
              

                <tr>
                <td> $nome</td>
                <td>$insegno</td>
                <td>$voto</td>
                <td>$idapp</td>
                <td> $ritirato</td>
                <td> $data</td>
          </tr>
              

                </div>
                
            ");  
             }
             
                
            
          
            ?>
                          </tbody>
</table>
                            </div>
</form>


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