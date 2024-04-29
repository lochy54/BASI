<?php



session_start();
$conn=pg_connect("host=localhost user=postgres port=5432 dbname=ProgettoUni password=7865");

if (!$conn) {
    session_destroy();
    $Message = urlencode("Errore connessione");
  header("Location: index.php?Message=".$Message);
  exit;
}


if(isset($_POST['radio1'])){
  $mat =  explode(",", $_POST['radio1'])[0];
  $idper = explode(",", $_POST['radio1'])[1];
  
}else{

  $mat=$_SESSION['mat'];
  $idper=$_POST['id1'];
}


$nome = $_POST['nome'];
$cognome = $_POST['cognome'];
$pass = $_POST['password'];

if(isset($_POST['lau'])){

  $sql="select laurea($1,$2)";
  $res = pg_prepare($conn,"q1",$sql);
  $res = pg_execute($conn, "q1", array($mat,$_POST['voto']));

}elseif(!isset($_POST['del'])){

  $sql="UPDATE public.persona
  SET  nome=$1, cognome=$2
WHERE idpersona=$3;";
  $res = pg_prepare($conn,"q3",$sql);
  $res = pg_execute($conn, "q3", array($nome,$cognome,$idper));

  $sql1="UPDATE public.studente
  SET password=$1
  WHERE matricola=$2 ;";
  $res = pg_prepare($conn,"q2",$sql1);
  $res = pg_execute($conn, "q2", array($pass,$mat));


}

else{

    $sql="delete from studente where matricola=$1";
    $res = pg_prepare($conn,"q1",$sql);
    $res = pg_execute($conn, "q1", array($mat));
}

if (!$res) {
  session_destroy();
  $Message = urlencode("Errorre db");
  header("Location: index.php?Message=".$Message);
}else{

  if(isset($_POST['mod'])){
    $Message = urlencode("Modifica effettuata ");
    header("Location: studenti.php?Message=".$Message);
  }else{

    $Message = urlencode("Modifica effettuata ");
    header("Location: mods.php?Message=".$Message);
  }

}


  ?>



