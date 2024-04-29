<?php

if(isset($_GET['Message'])){
    echo '<script type="text/javascript">
    window.onload = function () { alert("',$_GET["Message"],'"); } 
    </script>';
}


session_start();
$mat=$_SESSION['mat'];

if(isset($_SESSION['idcorso'])){
    $id = $_SESSION['idcorso'];
    unset($_SESSION['idcorso']); 

}elseif(isset($_SESSION['cod'])){

    $id=$_SESSION['cod'];

}else{
   
    $id=$_POST['idc'];

}

$_SESSION['cod']=$id;

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
select * from insegnamento where idcorso=$1";
$res = pg_prepare($conn,"q1",$sql);
$res = pg_execute($conn, "q1", array($id));

             
$sql1 = "
with a as(

    select count(codicedocente),codicedocente from insegnamento group by(codicedocente)
    
    )
    
    
    
    select persona.*, docente.codicedocente , a.count from persona right join docente ON docente.idpersona = persona.idpersona
    left join a on a.codicedocente=docente.codicedocente 
    ";
$res1 = pg_prepare($conn,"q2",$sql1);
$res1 = pg_execute($conn, "q2", array());





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
                        <a class="nav-link" href="segreteria.php">Segreteria</a>
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
        <h2>Benvenuto, <?php echo $nome; ?> <?php echo $cognome; ?>  , stai lavorando sul corso:  <?php echo $id; ?></h2>

        <form action='modccc.php' method='POST'>






        <div id="c3" style="width:30%; left:65%;">     







<div class="card mb-3">
<div class="card-header">
    Seleziona docente
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
        $count= $row1[6];
            print(" 

            <tr>
            <td>$idpersona</td>
            <td>$nome</td>
            <td>$cognome</td>
            <td>$mat</td>
            
            <td><input class='form-check-input' type='radio' name='radio1' id='radio1' value='$mat,$idpersona'></td>
            <td>$count</td>

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







<div id="c3" style="left:10%" >     







        <div class="card mb-3">
        <div class="card-header">
            Lista insegnamenti
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
      <th scope="col">Periodicità</th>
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
                    <td><button type='submit' class='btn btn-primary' name='control' id='control' value='$cod'>Gestisci</button></td>
                    </tr>

                 
                ");   
             }
             

            ?>
</tbody>
            </table>
          
            </div  >
       
            </div>
    </div>    







<div class="card mb-3"  style="left:51.2%; top:18%;position:absolute ;  ">
        <div class="card-header">
            Aggiungi Insegnamento
        </div>
        <div class='card-body'>
        <p><strong>Nome:</strong></p>
         <p> <input type="text"  id="nome" class="form-control" name="nome"></p>
         <p><strong>Crediti:</strong></p>
         <p> <input type="number"  id="crediti" class="form-control" name="crediti"></p>
         <p><strong>Descrizione:</strong></p>
         <p> <input type="text"  id="descrizione" class="form-control" name="descrizione"></p>
         <p><strong>Durata:</strong></p>
         <p> <input type="number"id="durata" class="form-control" name="durata"></p>
         <p><strong>Anno:</strong></p>
         <p> <input type="number"  id="anno" class="form-control" name="anno"></p>
         <p><button type="submit" class="btn btn-primary">Aggiungi corso</button></p>

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
