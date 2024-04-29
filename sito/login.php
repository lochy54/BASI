<?php


$password = $_POST['password'];
$user = $_POST['username'];
$selected = $_POST['domain'];
$domain= $_POST['domain'];

session_start();

$conn=pg_connect("host=localhost user=postgres port=5432 dbname=ProgettoUni password=7865");

if (!$conn) {
    session_destroy();
    $Message = urlencode("Errore connessione");
  header("Location: index.php?Message=".$Message);
  exit;
}

if($selected=='docenti'){

$sql = "SELECT codicedocente ,password FROM docente WHERE codicedocente = $1";
$res = pg_prepare($conn,"q1",$sql);

}else if($selected=='segreteria'){

$sql = "SELECT codicesegreteria ,password FROM segreteria WHERE codicesegreteria = $1";
$res = pg_prepare($conn,"q1",$sql);

}else{

 $sql = "SELECT matricola ,password FROM studente WHERE matricola = $1";
 $res = pg_prepare($conn,"q1",$sql);

}
$res = pg_execute($conn, "q1", array($user));

if (!$res) {
    session_destroy();
    $Message = urlencode("Errorre db");
    header("Location: index.php?Message=".$Message);
  }

$row = pg_fetch_assoc($res);
if($row['password']==$password){  
    $_SESSION["mat"] = $user; 
     header("Location: $domain.php");
}else{
    $Message = urlencode("Dati login sbagliati");
    header("Location: index.php?Message=".$Message);
    session_destroy();
 }

 exit;

  ?>



