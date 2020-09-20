<?php

class Autoloader
{
  protected $baseDir = '';



  public function set_baseDir(string $baseDir)
  {
    $this->baseDir = $baseDir;
    return $this;
  }



  public function register()
  {
    spl_autoload_register(function($class) {

      if (file_exists($this->baseDir . '/' . $class . '.php')) {
        require_once $this->baseDir . '/' . $class . '.php';
        return true;
      } else {
        $subDirs = glob($this->baseDir . '/*' , GLOB_ONLYDIR);
        foreach ($subDirs as $subDir) {
          if (file_exists($subDir . '/' . $class . '.php')) {
            require_once $subDir . '/' . $class . '.php';
          }
        }
      }
    });
  }
}

?>
