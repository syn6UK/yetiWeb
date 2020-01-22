<?php


namespace App\Controllers;


use PDO;
use PDOException;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\PhpRenderer;

class _Install_Controller
{


    public function __construct(PhpRenderer $renderer, $sql ) {
        $this->renderer = $renderer;
        $this->sql_directory = $sql;
    }


    public function InstallScreen(Request $request, Response $response, $args){
        return $this->renderer->render($response, 'admin/installer.phtml', $args);
    }


    public function runInstaller(Request $request, Response $response, $args){


        $body = $request->getParsedBody();
        $dsn = 'mysql:dbname='.$body['DB_NAME'].';host=' . $body['DB_URL'];


        try {
            $db = new PDO($dsn, $body['DB_USERNAME'], $body['DB_PASSWORD']);
        } catch (PDOException $e) {
            $args['body'] = $body;
            $args['error'] = $e->getMessage();
            return $this->renderer->render($response, 'admin/installer.phtml', $args);
        }


        // CONNECTION SUCCESSFUL PROCEED TO INSTALL


        $query = file_get_contents($this->sql_directory . "/install.sql");


        try {
            $db->exec($query);
        } catch (PDOException $e) {
            $args['body'] = $body;
            $args['error'] = $e->getMessage();
            return $this->renderer->render($response, 'admin/installer.phtml', $args);
        }

        return $this->renderer->render($response, 'admin/installed.phtml', $args);


    }

}