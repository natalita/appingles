<?php
$host = 'free.clusters.zeabur.com';
$user = 'root';
$pass = 'nq70foXg8G9Hxlu5b3V1AB6zJ2UpCve4';
$db = 'zeabur';
$port = '31513';

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