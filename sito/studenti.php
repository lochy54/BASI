<?php
session_start();
$mat=$_SESSION['mat'];




$conn=pg_connect("host=localhost user=postgres port=5432 dbname=ProgettoUni password=7865");

if (!$conn) {
    session_destroy();
    $Message = urlencode("Errore connessione");
  header("Location: index.php?Message=".$Message);
  exit;
}


$sql = "select * from studente inner join persona on persona.idpersona = studente.idpersona inner join corso_di_laurea ON corso_di_laurea.idcorso = studente.idcorso where matricola= $1";
$res = pg_prepare($conn,"q1",$sql);
$res = pg_execute($conn, "q1", array($mat));



if (!$res) {
    session_destroy();
    $Message = urlencode("Errorre db");
    header("Location: index.php?Message=".$Message);
  }
  
$row = pg_fetch_row($res);

$matricola = $row[0];        
$password = $row[1];           
$data_iscrizione = $row[2];    
$id_persona = $row[3];          
$id_corso = $row[4];            
$nome = $row[6];              
$cognome = $row[7];            
$cittadinascita = $row[8];     
$datadinascita = $row[9];     
$durata = $row[11];            
$descrizione = $row[12];       
$nome_2 = $row[13];            

$_SESSION['nome'] = $nome;
$_SESSION['cognome'] = $cognome;
$_SESSION['id_corso']= $id_corso;
$_SESSION['id_persona']= $id_persona;
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
                        <a class="nav-link" href="appelli.php">Appelli</a>
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

        <div class="card mb-3" >
            <div class="card-header">
                Dati Personali
            </div>
            <div class="card-body">
                <p><strong>Nome:</strong> <?php echo $nome; ?></p>
                <p><strong>Cognome:</strong> <?php echo $cognome; ?></p>
                <p><strong>Matricola:</strong> <?php echo $matricola; ?></p>
                <p><strong>Data inscrizione:</strong> <?php echo $data_iscrizione; ?></p>
                <p><strong>Data di nascita:</strong> <?php echo $datadinascita; ?></p>
                <p><strong>Città di nascita:</strong> <?php echo $cittadinascita; ?></p>
            </div>
        </div>


        


        <div class="card mb-3">
            <div class="card-header">
                Corso laurea
            </div>
            <div class="card-body">
                <p><strong>Corso di Laurea:</strong> <?php echo $nome_2; ?></p>
                <p><strong>Id corso:</strong> <?php echo $id_corso; ?></p>
                <p><strong>Durata:</strong> <?php echo $durata; ?> anni </p>
                <p><strong>descrizione:</strong> <?php echo $descrizione; ?></p>

            </div>
        </div>

    </div>


</div>   
<form action='del.php' method='POST'>      

<div class="card mb-3"  style="left:80%; top:18%;position:absolute;  ">
        <div class="card-header">
            Modifica studente
        </div>
        <div class='card-body'>
        <p><strong>Nome:</strong>
        <input type="text" min="1" max="100" id="nome" class="form-control" name="nome" value=<?php echo $nome; ?>></p>
         <p><strong>Cognome:</strong>
         <input type="text" min="1" max="100" id="cognome" class="form-control" name="cognome"  value=<?php echo $cognome; ?>></p>
         <input type="hidden" minlength="10" maxlength="10" id="id1" class="form-control" name="id1" readonly value=<?php echo $id_persona; ?> ></p> 
         <p><strong>Password:</strong>
         <input type="text" minlength="10" maxlength="10" id="password" class="form-control" name="password"  value=<?php echo $password; ?> ></p>
         <p><button type="submit" class="btn btn-primary" name="mod" id="mod" value=1>Modifica docente</button></p>


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
