<?php


function generateRandomID($length) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $id = '';

    for ($i = 0; $i < $length; $i++) {
        $randomIndex = rand(0, strlen($characters) - 1);
        $id .= $characters[$randomIndex];
    }

    return $id;
}

$randomID = generateRandomID(10);

session_start();






$nome= $_SESSION['nome'];
$cognome = $_SESSION['cognome'];

$conn=pg_connect("host=localhost user=postgres port=5432 dbname=ProgettoUni password=7865");

if (!$conn) {
    session_destroy();
    $Message = urlencode("Errore connessione");
  header("Location: index.php?Message=".$Message);
  exit;
}


if(isset($_POST['control'])||(isset($_SESSION['control']))){

    if(isset($_SESSION['control'])){
        $_POST['control']=$_SESSION['control'];
        unset($_SESSION['control']);
    }


?>




<?php 

if(isset($_GET['Message'])){
    echo '<script type="text/javascript">
    window.onload = function () { alert("',$_GET["Message"],'"); } 
    </script>';
}


$mat=$_SESSION['mat'];


$sql='select * from insegnamento
where idinsegnamento in
(SELECT idinsegnamento from propedeutico
where idinsegnamento_dipendente=$1
)';

$res = pg_prepare($conn,"q1",$sql);
$res = pg_execute($conn, "q1", array($_POST['control']));



$sql1='select * from insegnamento where idcorso=$1 and idinsegnamento not in 
(SELECT idinsegnamento from propedeutico where idinsegnamento_dipendente=$2) 
and idinsegnamento!=$2
and anno < (select anno from insegnamento where idinsegnamento=$2)
';

$res1 = pg_prepare($conn,"q2",$sql1);
$res1 = pg_execute($conn, "q2", array($_SESSION['cod'],$_POST['control']));


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
                        <a class="nav-link" href="modcc.php">Back</a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container mt-5" style="width:100%;" >
        <h2>Benvenuto, <?php echo $nome; ?> <?php echo $cognome; ?>, Stai lavorando su: <?php echo $_POST['control']; ?></h2>

        <form action='addccc.php' method='POST'>







        <div id="c3" style="left:7.5%" >     







<div class="card mb-3">
<div class="card-header">
    Lista propedeuticità
</div>
<div class='card-body'>
    <table class="table">
<thead class="thead-dark">
<tr>

<th scope="col">Nome</th>
<th scope="col">Crediti</th>
<th scope="col">Docente</th>
<th scope="col">Durata</th>
<th scope="col">Anno</th>
</tr>
</thead>
<tbody>




<?php

    
    while($row = pg_fetch_assoc($res)){


     
        $nome = $row['nome'];
        $crediti = $row['crediti'];
        $doc= $row['codicedocente'];
        $durata= $row['durata'];
        $anno=$row['anno'];
        $cod=$row['idinsegnamento'];
        
            print(" 


            <tr>
            <td>$nome</td>
            <td>$crediti</td>
            <td>$doc</td>
            <td>$durata</td>
            <td>$anno</td>
            </tr>

         
        ");   
     }
     

    ?>
</tbody>
    </table>
  
    </div  >
    </div>
    </div>




    <div id="c3" style="left:52.5%" >     



<div class="card mb-3 ">
<div class="card-header">
    Aggiungi prpedeuticità
</div>
<div class='card-body'>
    <table class="table">
<thead class="thead-dark">
<tr>

<th scope="col">Nome</th>
<th scope="col">Crediti</th>
<th scope="col">Docente</th>
<th scope="col">Durata</th>
<th scope="col">Anno</th>
<th scope="col">Aggiungi</th>
</tr>
</thead>
<tbody>




<?php
$c=$_POST['control'];
    
    while($row = pg_fetch_assoc($res1)){


     
        $nome = $row['nome'];
        $crediti = $row['crediti'];
        $doc= $row['codicedocente'];
        $durata= $row['durata'];
        $anno=$row['anno'];
        $cod=$row['idinsegnamento'];
        
            print(" 


            <tr>
            <td>$nome</td>
            <td>$crediti</td>
            <td>$doc</td>
            <td>$durata</td>
            <td>$anno</td>
            <td><button type='submit' class='btn btn-primary' name='control' id='control' value='$cod,$c'>Aggiungi</button></td>
            </tr>

         
        ");   
     }
     

    ?>
</tbody>
    </table>
  
    </div  >  

</div>

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





<?php


}else{

    $mat =  explode(",", $_POST['radio1'])[0];
    $idper = explode(",", $_POST['radio1'])[1];

    $sql='INSERT INTO public.insegnamento(
        idpersona, codicedocente, idcorso, idinsegnamento, nome, crediti, descrizione, durata, anno)
        VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)';

    $res = pg_prepare($conn,"q1",$sql);
    $res = pg_execute($conn, "q1", array($idper,$mat,$_SESSION['cod'],$randomID,$_POST['nome'],$_POST['crediti'],$_POST['descrizione'],$_POST['durata'],$_POST['anno']));

    if (!$res) {
  
        session_destroy();
        $Message = urlencode("Errorre db");
        header("Location: index.php?Message=".$Message);
        
 
      }else{
        $Message = urlencode("Aggiunto con successo");
        header("Location: modcc.php?Message=".$Message);
    
      }
   
 
}










  ?>



