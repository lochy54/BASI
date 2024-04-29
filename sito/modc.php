<?php

if(isset($_GET['Message'])){
    echo '<script type="text/javascript">
    window.onload = function () { alert("',$_GET["Message"],'"); } 
    </script>';
}


session_start();
unset($_SESSION['cod']);

$mat=$_SESSION['mat'];
$nome= $_SESSION['nome'];
$cognome = $_SESSION['cognome'];
if(isset($_SESSION['idcorso'])||isset($_POST['idcorso'])){
    
    header("Location: modcc.php");


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
                        <a class="nav-link" href="insertS.php">Inserisci studente</a>
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

                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5" style="width:100%;" >
        <h2>Benvenuto, <?php echo $nome; ?> <?php echo $cognome; ?> </h2>

<form action='modcc.php' method='POST'>

<div id="c1">





 
    </div>

    <div class="card mb-3" >
        <div class="card-header">
             Id Corso
            </div>

            <div class='card-body'>

<input type='text' class='form-control'  name='idc' id='idc'></td> </tr>


 <br>
    <button type="submit" class="btn btn-primary" > Modifica Corso</button>

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
