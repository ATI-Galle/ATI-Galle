<?php
define('DB_SERVER','localhost');
define('DB_USER','oqqcvjzs_ati');
define('DB_PASS','0774879564@Mano');
define('DB_NAME', 'oqqcvjzs_ati');
$con = mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
// Check connection
if (mysqli_connect_errno())
{
 echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
?>