<?php

namespace App\Controllers;

use App\Models\Web\BlogPost;
use App\Models\Web\CaseStudy;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\UploadedFile;
use Slim\Views\PhpRenderer;

class _CS_Controller{

    public $table;
    public $logger;
    public $renderer;
    public $adminRender;
    public $upload_Directory;

    public function __construct($logger, $db, PhpRenderer $renderer, PhpRenderer $adminRender, $upload ) {
        $this->table = $db;
        $this->logger = $logger;
        $this->renderer = $renderer;
        $this->adminRender = $adminRender;
        $this->upload_Directory = $upload;
    }

    public function getAllCaseStudys(Request $request, Response $response, $args){

        $args['css'] = CaseStudy::all();

        return $this->adminRender->render($response, 'admin/caseStudy.phtml', $args);

    }

    public function getSingleCaseStudy(Request $request, Response $response, $args){

        $args = CaseStudy::find($args['id'])->toArray();

        return $this->adminRender->render($response, 'admin/singleCase.phtml', $args);

    }

    public function editCaseStudy(Request $request, Response $response, $args){

        $post = CaseStudy::find($args['id']);

        $body = $request->getParsedBody();

        foreach($body as $key => $value){

            $post->{$key} = $value;

        }

        $post->save();

        return $response->withRedirect('/admin/case-studies/' . $args['id']);

    }


    public function getAllCaseStudysPublic(Request $request, Response $response, $args){

        $args['posts'] = CaseStudy::all();

        return $this->renderer->render($response, 'caseStudy.phtml', $args);

    }

    public function getSingleCaseStudyPublic(Request $request, Response $response, $args){

        $args['posts'] = CaseStudy::where([
            ["url", '=', $args['slug']]
        ])->first();

        if($args['posts']){
            return $this->renderer->render($response, 'singleCase.phtml', $args);
        }else{
            $args['title'] = 'ERROR 404';
            $response = $response->withStatus(404);
            return $this->renderer->render($response, '404.phtml', $args);
        }


    }


    public function CreateCaseStudy(Request $request, Response $response, $args){

        $body = $request->getParsedBody();
        $files = $request->getUploadedFiles();

        foreach ($files as $key => $uploadedFile){

            if ($uploadedFile->getSize() > 0) {
                 $filename = $this->moveUploadedFile($this->upload_Directory, $uploadedFile);
                 $body[$key] = "https://".$_SERVER['HTTP_HOST']."/src/uploads/" . $filename;
            }

        }

        $createBlog = CaseStudy::create($body);

        if($createBlog){

            return $response->withRedirect('/admin/case-studies/'.$createBlog->id, 200);

        }else{
            return $response->withRedirect('/admin/case-studies');
        }

    }

    public function moveUploadedFile($directory, UploadedFile $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        echo $directory . DIRECTORY_SEPARATOR . $filename;

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }


}