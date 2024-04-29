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




             
$sql1 = "with a as(

    select count(codicedocente),codicedocente from insegnamento group by(codicedocente)
    
    )
    
    
    
    select persona.*, docente.codicedocente, docente.password , a.count from persona right join docente ON docente.idpersona = persona.idpersona
    left join a on a.codicedocente=docente.codicedocente 
    ";
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


    <script>
            
            function check(a,b,c) {
                document.getElementById('nome').value=a;
                document.getElementById('cognome').value=b;
                document.getElementById('password').value=c;
            
            }
                </script>


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

                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="InsertS.php">Inserisci studente</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="mods.php">Modifica studente</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="insertd.php">Inserisci docente</a>
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

<form action='del1.php' method='POST'>







<div id="c1">     







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
      <th scope="col">Mat</th>
      <th scope="col">Seleziona</th>
      <th scope="col">N</th>
    </tr>
  </thead>
  <tbody>






        
        <?php
 
            
            while($row1 = pg_fetch_row($res1)){


                $idpersona = $row1[0];
                $nome = $row1[1];
                $cognome = $row1[2];
                $mat= $row1[5];
                $pass= $row1[6];
                $n= $row1[7];
                    print(" 

                    <tr>
                    <td>$idpersona</td>
                    <td>$nome</td>
                    <td>$cognome</td>
                    <td>$mat</td>
                    <td>   <input class='form-check-input' type='radio' name='radio1' id='radio1' value='$mat,$idpersona' onclick='check(\"$nome\", \"$cognome\", \"$pass\")'></td>
                    <td>$n</td>

                    </tr>

              ");   
             }
             

            ?>
</tbody>
            </table>

            </div  >
            </div  >




            </div>


    </div>    






    <div class="card mb-3"  style="left:80%; top:18%;position:absolute;  ">
        <div class="card-header">
            Modifica docente
        </div>
        <div class='card-body'>
        <p><strong>Nome:</strong></p>
         <p> <input type="text" min="1" max="100" id="nome" class="form-control" name="nome"  ></p>
         <p><strong>Cognome:</strong></p>
         <p> <input type="text" min="1" max="100" id="cognome" class="form-control" name="cognome"  ></p>
         <p><strong>Password:</strong></p>
         <p> <input type="text" minlength="10" maxlength="10" id="password" class="form-control" name="password"  ></p>
         <p><button type="submit" class="btn btn-primary">Modifica docente</button></p>
         <p><button type="submit" class="btn btn-primary" value="1" name="del" id="del">Elimina docente</button></p>

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
