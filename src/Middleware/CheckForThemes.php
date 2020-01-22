<?php

namespace App\Middleware;


use Slim\Http\Response;

class CheckForThemes
{

    private $themes;

    public function __construct($container) {

        $this->themes = $container->get('theme_directory');

    }

    public function __invoke($request, Response $response, $next)
    {

        if($this->dir_is_empty($this->themes)){

            $route = $request->getAttribute('route')->getName();

            if($route !== 'installer' && $route !== 'themes'){
                return $response->withRedirect('/admin/themes');
            }

        }

    }

    private function dir_is_empty($dir) {
        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                closedir($handle);
                return FALSE;
            }
        }
        closedir($handle);
        return TRUE;
    }

}