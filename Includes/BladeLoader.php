<?php
namespace GammaTileplanner\Includes;

use Jenssegers\Blade\Blade;

class BladeLoader
{
    private static $instance = NULL;
    private $blade;

    private function __construct() {
      $this->blade = new Blade(GAMMATILEPLANNER_PATH . 'resources/views', GAMMATILEPLANNER_PATH . 'resources/cache');
    }

    // Clone not allowed
    private function __clone() { }

    public static function getInstance() {
      if ( is_null(self::$instance) ) {
        self::$instance = new BladeLoader();
      }
      return self::$instance;
    }

    public function template( $name, $args=array() ) {
      return $this->blade->render($name, $args);
    }

}
