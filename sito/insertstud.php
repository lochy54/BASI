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
$randompass = generateRandomID(10);



$idc=$_POST['radeo'];
$id=$_POST['radeo1'];





session_start();

$conn=pg_connect("host=localhost user=postgres port=5432 dbname=ProgettoUni password=7865");

if (!$conn) {
    session_destroy();
    $Message = urlencode("Errore connessione");
  header("Location: index.php?Message=".$Message);
  exit;
}

$sql = "INSERT INTO public.studente(
	matricola, password, idpersona, idcorso)
	VALUES ($1, $2, $3, $4);";

$res = pg_prepare($conn,"q1",$sql);
$res = pg_execute($conn, "q1", array($randomID,$randompass,$id,$idc));




if (!$res) {
    session_destroy();
    $Message = urlencode("Errorre db");
    header("Location: index.php?Message=".$Message);
  }else{
    $Message = urlencode("studente aggiunto con matricola ".$randomID." e password ".$randompass);
    header("Location: InsertS.php?Message=".$Message);

  }




  ?>



