<?php

include 'inc/config.inc.php';
include 'inc/autoloader.php';



$conn = new Conn(...$config['db_access']);
$structure = new Structure($conn);



try {
  echo '<pre>';
  print_r($conn);
  print_r($structure);
  echo '</pre>';

} catch(customException $e) {
  echo $e->errorMessage();
}


?>
