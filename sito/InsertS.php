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
select * from corso_di_laurea";
$res = pg_prepare($conn,"q1",$sql);
$res = pg_execute($conn, "q1", array());





if (!$res) {
    session_destroy();
    $Message = urlencode("Errorre db");
    header("Location: index.php?Message=".$Message);
  }


              
$sql1 = "
select * from persona where idpersona not in (select p.idpersona from persona as p inner join studente_storico as st on st.idpersona = p.idpersona)
except
select persona.* from persona INNER join studente on studente.idpersona = persona.idpersona";
$res1 = pg_prepare($conn,"q2",$sql1);
$res1 = pg_execute($conn, "q2", array());





if (!$res1) {
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
                        <a class="nav-link" href="segreteria.php">Segreteria</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="mods.php">Modifica studente</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="insertd.php">Inserisci docente</a>
                    </li>

        
                    <li class="nav-item">
                        <a class="nav-link" href="modd.php">Modifica docente</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="insertc.php">Inserisci corso</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="modc.php">Modifica corso</a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5" style="width:100%;" >
        <h2>Benvenuto, <?php echo $nome; ?> <?php echo $cognome; ?> </h2>

<form action='insertstud.php' method='POST'>





<div id="c2">

    
        <div class="card mb-3">
            <div class="card-header">
                Seleziona corso
            </div>

            
        <div class='card-body'>
            <table class="table">
  <thead class="thead-dark">
    <tr>
      <th scope="col">Codice</th>
      <th scope="col">Nome</th>
      <th scope="col">Seleziona</th>

    </tr>
  </thead>
  <tbody>



            <?php
            



            while($row = pg_fetch_assoc($res)){


                $idcorso = $row['idcorso'];
                $nome = $row['nome'];

                    print(" 
              
                    
                    <tr>
                    <td> $idcorso</td>
                    <td>$nome</td>
                    <td>  <input class='form-check-input' type='radio' name='radeo' id='radeo' value=$idcorso></td>
            
              </tr>
                  


                    ");   
             }
             
            ?>
                          </tbody>
</table>
                            </div>

        </div>

        </div>
<div id="c3">     






        <div class="card mb-3">
        <div class="card-header">
            Seleziona persona
        </div>

        <div class='card-body'>
            <table class="table">
  <thead class="thead-dark">
    <tr>
      <th scope="col">Id persona</th>
      <th scope="col">Nome</th>
      <th scope="col">Cognome</th>
      <th scope="col">Seleziona</th>
    </tr>
  </thead>
  <tbody>
    


        
        <?php
            
            
            while($row1 = pg_fetch_row($res1)){


                $idpersona = $row1[0];
                $nome = $row1[1];
                $cognome = $row1[2];


                    print(" 
                    
    
                



                    <tr>
                    <td> $idpersona</td>
                    <td>$nome</td>
                    <td>$cognome</td>
                    <td>      <input class='form-check-input' type='radio' name='radeo1' id='radeo1' value=$idpersona></td>
            
              </tr>

                ");   
             }
             
            ?>
                          </tbody>
</table>
                            </div>
  
            </div  >
            </div  >







    </div>    


    <div id="c4" style="left:83.8%;">
<p><button type="submit" class="btn btn-primary">Crea studente</button></p>

</div>


    </form>


    <form action='creaper.php' method='POST'>


    <div class="card mb-3"  style="left: 83%; top:18%;position:absolute;  ">
        <div class="card-header">
            Crea persona
        </div>
        <div class='card-body'>
        <p><strong>Nome:</strong></p>
         <p> <input type="text" min="1" max="100" id="nome" class="form-control" name="nome"></p>
         <p><strong>Cognome:</strong></p>
         <p> <input type="test" min="1" max="100" id="cognome" class="form-control" name="cognome"></p>
         <p><strong>Città di nascita:</strong></p>
         <p> <input type="test" min="1" max="100" id="citt" class="form-control" name="citt"></p>
         <p><strong>Data di nascita:</strong></p>
         <p> <input type="datetime-local" class="form-control" id="data" name="data"></p>
         <p><button type="submit" class="btn btn-primary">Crea persona</button></p>

        </div>
    </div>

            </div>
            </div>




</form>


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
