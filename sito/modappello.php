<?php


$matr=$_POST['matr'];

$voto=$_POST['voto'];
$id=$_POST['id'];

$rit='false';



if($_POST['rit']=="on"){
  $rit='true';
}

session_start();

$conn=pg_connect("host=localhost user=postgres port=5432 dbname=ProgettoUni password=7865");

if (!$conn) {
    session_destroy();
    $Message = urlencode("Errore connessione");
  header("Location: index.php?Message=".$Message);
  exit;
}

$sql = "UPDATE public.provare
SET  voto=$3, ritirato=$4
WHERE idappello=$1 and matricola=$2;";
$res = pg_prepare($conn,"q1",$sql);
$res = pg_execute($conn, "q1", array($id,$matr,$voto,$rit));




if (!$res) {
  session_destroy();
  $Message = urlencode("Errorre db");
  header("Location: index.php?Message=".$Message);
}else{
  $Message = urlencode("salvato");
  header("Location: gestP.php?Message=".$Message);
  

}






  ?>

