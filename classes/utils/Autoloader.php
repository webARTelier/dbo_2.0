<?php

class Autoloader
{
  protected $baseDir = '';



  function __construct(string $baseDir)
  {
    $this->baseDir = $baseDir;
    $this->register();
  }



  private function register()
  {
    spl_autoload_register(function ($class) {

      if (file_exists($this->baseDir . '/' . $class . '.php')) {
        require_once $this->baseDir . '/' . $class . '.php';
        return true;
      }

      $subDirs = glob($this->baseDir . '/*', GLOB_ONLYDIR);
      foreach ($subDirs as $subDir) {
        if (file_exists($subDir . '/' . $class . '.php')) {
          require_once $subDir . '/' . $class . '.php';
          return true;
        }
      }
    });
  }
}
