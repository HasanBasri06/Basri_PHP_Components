<?php 

namespace Basri\Router;

use Config;
use Pug\Pug;

class View {
    private static $pug;
    
    public function __construct() {
        self::$pug = new Pug([
            'cache' => storage_path() . '/views/',
            'pretty' => true,
        ]);
    }
    
    public static function make($name, $data = []) {
        $DI = ['asset' => public_path()];
        $mergeData = array_merge($DI, $data);
        $route = Config::view()['path'] . '/' . $name . ".pug";
        $pug = new Pug([
            'pretty' => true,
            'cache' => storage_path() . '/views/',
        ]);

        return $pug->renderFile($route, $mergeData);
    }
}