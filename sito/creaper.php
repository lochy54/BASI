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



$nome=$_POST['nome'];
$cognome=$_POST['cognome'];
$citt=$_POST['citt'];
$data=$_POST['data'];



session_start();

$conn=pg_connect("host=localhost user=postgres port=5432 dbname=ProgettoUni password=7865");

if (!$conn) {
    session_destroy();
    $Message = urlencode("Errore connessione");
  header("Location: index.php?Message=".$Message);
  exit;
}

$sql = "INSERT INTO public.persona(
	idpersona, nome, cognome, cittadinascita, datadinascita)
	VALUES ($1, $2, $3, $4, $5);";

$res = pg_prepare($conn,"q1",$sql);
$res = pg_execute($conn, "q1", array($randomID,$nome,$cognome,$citt,$data));



if (!$res) {
    session_destroy();
    $Message = urlencode("Errorre db");
    header("Location: index.php?Message=".$Message);

}elseif(isset($_POST['cre'])){
      
      $Message = urlencode("persona aggiunta");
      header("Location: insertd.php?Message=".$Message);

  }else{

    
    $Message = urlencode("persona aggiunta");
    header("Location: InsertS.php?Message=".$Message);

  }



  ?>



