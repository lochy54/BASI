
<?php


session_start();
$id1 = explode(",",$_POST['control'])[0];
$id = explode(",",$_POST['control'])[1];
$_SESSION['control']=$id;
$conn=pg_connect("host=localhost user=postgres port=5432 dbname=ProgettoUni password=7865");

if (!$conn) {
    session_destroy();
    $Message = urlencode("Errore connessione");
  header("Location: index.php?Message=".$Message);
  exit;
}

$sql = "
INSERT INTO public.propedeutico(
	idinsegnamento, idinsegnamento_dipendente)
	VALUES ($1, $2)";

$res = pg_prepare($conn,"q1",$sql);
$res = pg_execute($conn, "q1", array($id1,$id));



if (!$res) {
  session_destroy();
  $Message = urlencode("Errorre db");
  header("Location: index.php?Message=".$Message);
}else{
    $Message = urlencode("Aggiunto con sucesso");
    header("Location: modccc.php?Message=".$Message);

} 



  ?>



