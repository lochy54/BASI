<?php
session_start();
session_destroy();
$Message = urlencode("Logout effettuato");

header("Location: index.php?Message=".$Message);

?>