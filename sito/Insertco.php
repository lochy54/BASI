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
$mat=$_SESSION['mat'];

$nome= $_SESSION['nome'];
$cognome = $_SESSION['cognome'];

$n = $_POST['nome'];
$d = $_POST['Descrizione'];
$du = $_POST['Durata'];

$conn=pg_connect("host=localhost user=postgres port=5432 dbname=ProgettoUni password=7865");

if (!$conn) {
    session_destroy();
    $Message = urlencode("Errore connessione");
  header("Location: index.php?Message=".$Message);
  exit;
}


$sql = "
INSERT INTO public.corso_di_laurea(
	idcorso, durata, descrizione, nome)
	VALUES ($1, $2, $3, $4)";
$res = pg_prepare($conn,"q1",$sql);
$res = pg_execute($conn, "q1", array($randomID,$du,$d,$n));



if (!$res) {
    session_destroy();
    $Message = urlencode("Errorre db");
    header("Location: index.php?Message=".$Message);
  }else{
    $Message = urlencode("Corso aggiunto");
    $_SESSION['idcorso']=$randomID;
    header("Location: modc.php?Message=".$Message);

  }


?>