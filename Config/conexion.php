<?php
$host = 'http://sfo1.clusters.zeabur.com/';
$user = 'root';
$pass = 'v3QbGIfp5n1RsZ869dkW2Ae7XoJz04MF';
$db = 'zeabur';
$port = '30409';

$con = mysqli_connect($host, $user, $pass, $db, $port);

if (!$con) {
    die('Error de conexión: ' . mysqli_connect_error());
}
?>
<?php
// $host = 'localhost';
// $user = 'root';
// $pass = '';
// $db = 'app_ingles';

// $con = mysqli_connect($host, $user, $pass, $db);

// if (!$con) {
//     die('Error de conexión: ' . mysqli_connect_error());
// }
?>
