<?php

namespace App\Controllers;

use Exception;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Models\Users\User as User;

class _User_Controller{

	public $table;

	public function __construct($logger, $db) {
		$this->table = $db;
		$this->logger = $logger;
	}

	// IF PASSWORD FLOW TOKEN IS USED, YOU WILL GET BACK THE CURRENT USER

	public function getCurrentUser(Request $request, Response $response, $args){

		$userid = $request->getAttribute("loggedInUser");

		if(is_null($userid)){
			return $response->withJSON((object)[
				"Error" => "Your are not authenticated as a user"
			],400);
		}

		return $response->withJSON(User::find($userid),200);

	}


}