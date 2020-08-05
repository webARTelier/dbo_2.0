<?php

// autoload general classes
// ------------------------
function loadClass($class)
{
  $class = 'general/classes/' . str_replace( '..', '', $class . '.php');

  if (file_exists($class)) {
    require_once $class;
  } else {
    return false;
  }
}



// autoload dbo interfaces
// -----------------------
function loadDboInterface($interface)
{
  $interface = 'dbo/interfaces/' . str_replace( '..', '', $interface . '.php');

  if (file_exists($interface)) {
    require_once $interface;
  } else {
    return false;
  }
}



// autoload dbo classes
// --------------------
function loadDboClass($class)
{
  $class = 'dbo/classes/' . str_replace( '..', '', $class . '.php');

  if (file_exists($class)) {
    require_once $class;
  } else {
    return false;
  }
}



spl_autoload_register('loadClass');
spl_autoload_register('loadDboInterface');
spl_autoload_register('loadDboClass');

?>
