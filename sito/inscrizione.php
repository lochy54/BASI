<?php


session_start();

$conn=pg_connect("host=localhost user=postgres port=5432 dbname=ProgettoUni password=7865");


$app = $_POST['id_app'];
$mat=$_SESSION['mat'];
$codp=$_SESSION['id_persona'];


if (!$conn) {
    session_destroy();
    $Message = urlencode("Errore connessione");
  header("Location: index.php?Message=".$Message);
  exit;
}


$sql = "INSERT INTO public.provare(
	idappello, idpersona, matricola)
	VALUES ($1, $2, $3);";
$res = pg_prepare($conn,"q1",$sql);


$res = pg_execute($conn, "q1", array($app,$codp,$mat));




if (!$res) {
    session_destroy();
    $Message = urlencode("Errorre db");
    header("Location: index.php?Message=".$Message);
  }else{
    $Message = urlencode("inscritto con sucesso");
    header("Location: appelli.php?Message=".$Message);

  }




  ?>



