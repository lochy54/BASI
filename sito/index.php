<?php
 if(isset($_GET['Message'])){
    echo '<script type="text/javascript">
    window.onload = function () { alert("',$_GET["Message"],'"); } 
    </script>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UNI</title>
   
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">

</head>
<body>
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4"  style="position: absolute; left: 10%; top: 10%;">
        <h1>Benvenuti all'Università</h1>
        <p></p>
    </div>

    
<div id="c1">
    <div class="container mt-4"  style="position: absolute; left: 10%; top: 25%; width: 70%;"
>
        <h2>Seleziona il tuo tipo di accesso:</h2>
        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="domain-select" class="form-label">Tipo di Accesso:</label>
                <select class="form-select" id="domian" name="domain" required>
                    <option value="docenti">Docenti</option>
                    <option value="segreteria">Segreteria</option>
                    <option value="studenti">Studenti</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Accedi</button>
        </form>
    </div>





</div>

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
