<?php

use App\Controllers\_Blog_Controller;
use App\Controllers\_CS_Controller;
use App\Controllers\_Install_Controller;
use App\Controllers\_Lead_Controller;
use App\Models\Web\BlogPost;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controllers\_Auth_Controller;
use App\Controllers\_User_Controller;


$activeTheme = new \App\Modules\ThemeEngine();

// Routes

$app->get('/', _Blog_Controller::class.':homePageBlog');




$app->get('/contact', function (Request $request, Response $response, array $args) use($activeTheme) {
	// Render index view - Typically an API welcome page or JSON message.
	return $this->renderer->render($response, 'contact.phtml', $args);
});

$app->get('/thank-you', _Lead_Controller::class.':contactThanks');
$app->get('/thank-you-lead', _Lead_Controller::class.':leadThanks');


$app->post('/contact', _Lead_Controller::class.':contactPost');
$app->post('/lead', _Lead_Controller::class.':leadPost');


$app->get('/about', function (Request $request, Response $response, array $args) use($activeTheme) {

    $args['title'] = 'About Us';
	// Render index view - Typically an API welcome page or JSON message.
	return $this->renderer->render($response, $activeTheme->themeDirectory . 'about.phtml', $args);
});

$app->get('/services', function (Request $request, Response $response, array $args) use($activeTheme){
    $args['title'] = 'Our Services';

    // Render index view - Typically an API welcome page or JSON message.
	return $this->renderer->render($response, 'services.phtml', $args);
});

$app->group('/case-studies', function () {

    $this->get('', _CS_Controller::class.':getAllCaseStudysPublic');
    $this->get('/{slug}', _CS_Controller::class.':getSingleCaseStudyPublic');

});

$app->group('/blog', function () {

    $this->get('', _Blog_Controller::class.':getAllBlogPostsPublic');
    $this->get('/{slug}', _Blog_Controller::class.':getSingleBlogPostPublic');

});

$app->get('/team', function (Request $request, Response $response, array $args) use($activeTheme){
	// Render index view - Typically an API welcome page or JSON message.
	return $this->renderer->render($response, 'team.phtml', $args);
});

$app->group('/admin', function(){

    $this->get('', function (Request $request, Response $response, array $args) {

        return $this->adminRenderer->render($response, 'admin/dashboard.phtml', $args);

    });

    $this->get('/themes', function (Request $request, Response $response, array $args) {

        return $this->adminRenderer->render($response, 'admin/themes.phtml', $args);

    })->setName('themes');

    $this->group('/blog', function () {

        $this->get('', _Blog_Controller::class.':getAllBlogPosts');
        $this->get('/create', function (Request $request, Response $response, array $args) {
            return $this->adminRenderer->render($response, 'admin/singleBlog.phtml', $args);
        });
        $this->post('/create', _Blog_Controller::class.':createBlogPost');
        $this->get('/{id}', _Blog_Controller::class.':getSingleBlogPost');
        $this->post('/{id}', _Blog_Controller::class.':editBlogPost');

    }); 

    $this->group('/leads', function () {

        $this->get('', _Lead_Controller::class.':getAllLeads');
        $this->get('/{id}', _Blog_Controller::class.':getSingleLead');

    });

    $this->group('/case-studies', function () {

        $this->get('', _CS_Controller::class.':getAllCaseStudys');
        $this->get('/create', function (Request $request, Response $response, array $args) {
            return $this->adminRenderer->render($response, 'admin/singleCase.phtml', $args);
        });
        $this->post('/create', _CS_Controller::class.':createCaseStudy');
        $this->get('/{id}', _CS_Controller::class.':getSingleCaseStudy');
        $this->post('/{id}', _CS_Controller::class.':editCaseStudy');

    });

})->add(new Tuupola\Middleware\HttpBasicAuthentication([
    "users" => [
        "nick" => "yeti2020!",
        "joe" => "Sienna14!",
        "rob" => "gvlmnx320A!!"
    ]
]));


$app->group('/install', function () {

    $this->get('', _Install_Controller::class.':installScreen')->setName('installer');


});

$app->add('App\Middleware\CheckInstalled');


$app->options('/{routes:.+}', function ($request, $response, $args) {
	return $response;
});

$app->add(function ($req, $res, $next) {
	$response = $next($req, $res);
	return $response
		->withHeader('Access-Control-Allow-Origin', '*')
		->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
		->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
	$handler = $this->notFoundHandler; // handle using the default Slim page not found handler
	return $handler($req, $res);
});