<?php

namespace App\Middleware;


use Slim\Http\Response;

class CheckInstalled
{

    private $settings;

    public function __construct($container) {

        $this->settings = $container->get('settings');

    }

    public function __invoke($request, Response $response, $next)
    {

        foreach($this->settings['db'] as $key => $value){

            if($key !== 'prefix' && $value === ''){

                if($request->getAttribute('route')->getName() !== 'installer'){
                    return $response->withRedirect('/install');
                }

            }

        }

        if($request->getAttribute('route')->getName() !== 'installer'){
            return $next($request, $response);
        }else{
            return $response->withRedirect('/');
        }


    }

}