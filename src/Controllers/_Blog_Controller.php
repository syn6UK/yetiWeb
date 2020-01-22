<?php

namespace App\Controllers;

use App\Models\Web\BlogPost;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\UploadedFile;
use Slim\Views\PhpRenderer;

class _Blog_Controller{

	public $table;
	public $logger;
	public $renderer;
	public $adminRender;
	public $upload_Directory;

	public function __construct($logger, $db, PhpRenderer $renderer,PhpRenderer $adminRender, $upload ) {
		$this->table = $db;
		$this->logger = $logger;
		$this->renderer = $renderer;
		$this->adminRender = $adminRender;
		$this->upload_Directory = $upload;
	}

	public function homePageBlog(Request $request, Response $response, $args){

        $args['posts'] = BlogPost::orderBy('id', 'desc')->take(7)->get()->toArray();

        return $this->renderer->render($response, 'index.phtml', $args);

    }

	public function CreateBlogPost(Request $request, Response $response, $args){

	    $body = $request->getParsedBody();
        $files = $request->getUploadedFiles();


        foreach ($files as $key => $uploadedFile){

            if ($uploadedFile->getSize() > 0) {
                $filename = $this->moveUploadedFile($this->upload_Directory, $uploadedFile);
                $body[$key] = "https://".$_SERVER['HTTP_HOST']."/src/uploads/" . $filename;
            }

        }


        $createBlog = BlogPost::create($body);

        if($createBlog){

            return $response->withRedirect('/admin/blog/'.$createBlog->id, 200);

        }

    }

	public function getAllBlogPosts(Request $request, Response $response, $args){

        $args['posts'] = BlogPost::all();

        return $this->adminRender->render($response, 'blogList.phtml', $args);

    }

	public function getSingleBlogPost(Request $request, Response $response, $args){

        $args = BlogPost::find($args['id'])->toArray();

        return $this->adminRender->render($response, 'singleBlog.phtml', $args);

    }

	public function editBLogPost(Request $request, Response $response, $args){

        $post = BlogPost::find($args['id']);

        $body = $request->getParsedBody();

        $files = $request->getUploadedFiles();

        foreach ($files as $key => $uploadedFile){

            if ($uploadedFile->getSize() > 0) {
                $filename = $this->moveUploadedFile($this->upload_Directory, $uploadedFile);
                $body[$key] = "https://".$_SERVER['HTTP_HOST']."/src/uploads/" . $filename;
            }

        }


        foreach($body as $key => $value){

            $post->{$key} = $value;

        }

        $post->save();

        return $response->withRedirect('/admin/blog/' . $args['id']);

    }


	public function getAllBlogPostsPublic(Request $request, Response $response, $args){

        $args['posts'] = BlogPost::all();

        return $this->renderer->render($response, 'blogList.phtml', $args);

    }

	public function getSingleBlogPostPublic(Request $request, Response $response, $args){

        $args['posts'] = BlogPost::where([
            ["url", '=', $args['slug']]
        ])->first();

        if($args['posts']){
            return $this->renderer->render($response, 'singleBlog.phtml', $args);
        }else{
            $args['title'] = 'ERROR 404';
            $response = $response->withStatus(404);
            return $this->renderer->render($response, '404.phtml', $args);
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