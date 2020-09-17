<?php

// autoload classes
// ----------------
function loadClass($class)
{
  $mainFolder = 'classes';

  if (file_exists($mainFolder . '/' . $class . '.php')) {
    require_once $mainFolder . '/' . $class . '.php';
    return true;
  } else {
    $subFolders = glob($mainFolder . '/*' , GLOB_ONLYDIR);
    foreach ($subFolders as $subFolder) {
      if (file_exists($subFolder . '/' . $class . '.php')) {
        require_once $subFolder . '/' . $class . '.php';
      }
    }
  }
}



spl_autoload_register('loadClass');

?>
