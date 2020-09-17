<?php

// autoload general classes
// ------------------------
function loadClass($class)
{
  $mainFolder = 'classes';
  $subFolders = glob($mainFolder . '/*' , GLOB_ONLYDIR);

  foreach ($subFolders as $subFolder) {

    if (file_exists($subFolder . '/' . $class . '.php')) {
      require_once $subFolder . '/' . $class . '.php';
    }
  }
}



spl_autoload_register('loadClass');

?>
