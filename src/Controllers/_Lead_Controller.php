<?php

namespace App\Controllers;

use App\Models\Web\Lead;
use App\Modules\ThemeEngine;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\PhpRenderer;

class _Lead_Controller{

    public $table;
    public $logger;
    public $renderer;
    public $adminRender;
    protected $apikey = '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';
    public $activeTheme;


    public function __construct($logger, $db, PhpRenderer $renderer, PhpRenderer $adminRender ) {
        $this->table = $db;
        $this->logger = $logger;
        $this->renderer = $renderer;
        $this->adminRender = $adminRender;
        $themeEngine = new ThemeEngine();
        $this->activeTheme = $themeEngine->themeDirectory;

    }

    public function getAllLeads(Request $request, Response $response, $args){

        $args['posts'] = Lead::all();

        return $this->adminRender->render($response, 'admin/leadList.phtml', $args);

    }

    public function getSingleLead(Request $request, Response $response, $args){

        $args['posts'] = Lead::find($args['id']);

        return $this->adminRender->render($response, 'admin/singleLead.phtml', $args);

    }

    public function leadPost(Request $request, Response $response, $args){

        $body = $request->getParsedBody();

        $lead = Lead::create([
            "data" => json_encode($body),
            "type" => 'Lead Form Submission'

        ]);

        $url = $_SERVER['HTTP_HOST'] . '/admin/leads/' . $lead->id;

        $this->sendNotification($url, 'Lead Form Submission');

        return $response->withRedirect('/thank-you-lead');

    }

    public function contactThanks(Request $request, Response $response, $args){

        $args['title'] = 'Thank you';
        return $this->renderer->render($response, $this->activeTheme . 'contactThanks.phtml', $args);


    }

    public function leadThanks(Request $request, Response $response, $args){

        $args['title'] = 'Thank You';
        return $this->renderer->render($response, $this->activeTheme . 'leadThanks.phtml', $args);


    }


    public function contactPost(Request $request, Response $response, $args){

        $body = $request->getParsedBody();

        $lead = Lead::create([
            "data" => json_encode($body),
            "type" => 'Contact Form Submission'
        ]);

        $url = $_SERVER['HTTP_HOST'] . '/admin/leads/' . $lead->id;

        $this->sendNotification($url, 'Contact Form Submission');

        return $response->withRedirect('/thank-you');

    }


    private function sendNotification($url, $type){

        $sendgrid = new \SendGrid($this->apikey);

        $email = new\SendGrid\Mail\Mail();

        $email->setTemplateId('d-c0e46439953a4c8f9cb53898f0a40c76');
        $email->addTo("rob@nulead.co.uk");
        $email->addTo("joe@yetimediagroup.co.uk");
        $email->addTo("nick@yetimediagroup.co.uk");
        $email->setFrom("lead@yetimediagroup.co.uk", "Yeti Media Group");
        $email->addDynamicTemplateData( 'leadLink', $url);
        $email->addDynamicTemplateData( 'leadType', $url);

        return $sendgrid->send($email);

    }


}