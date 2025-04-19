<?php

$Usuario = 'u881807095_system';
$Contraseña = 'Brenda1994.';
$DataBase = 'dbname=u881807095_adecap;';
$Servidor = 'mysql:host=localhost;';
$JuegoCaract = 'charset=utf8';

try {
    $dbh = new PDO($Servidor . $DataBase . $JuegoCaract, $Usuario, $Contraseña);

    $stmt = $dbh->prepare('CALL insertarnuevosusuarios()');

    $stmt->execute();

    echo "Inserción de usuarios realizada correctamente.";

    $dbh = null;
} catch (PDOException $e) {
    print "¡Error!: " . $e->getMessage() . "<br/>";
    die();
}
?>
