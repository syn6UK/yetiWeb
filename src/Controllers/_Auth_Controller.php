<?php

namespace App\Controllers;

use Exception;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Models\Users\User as User;
use App\Models\Auth\oAuthAccessToken as Token;
use App\Models\Auth\oAuthClient as Client;

class _Auth_Controller{

	public $table;

	public function __construct($logger, $db) {
		$this->table = $db;
		$this->logger = $logger;
	}

	public function processTokenRequest(Request $request, Response $response, $args){

		$body = $request->getParsedBody();

		if(!isset($body['client_id'])){
			return $response->withJSON((object)[
				"Error" => "No client id has been specified. Use 'client_id'"
			],400);

		}

		if(!isset($body['client_secret'])){

			return $response->withJSON((object)[
				"Error" => "No client secret has been specified. Use 'client_secret'"
			],400);

		}

		switch($body['grant_type']){

			case "client_credentials":
				$grantResult = $this->clientCredentialsFlow($body);
				break;

			case "password":
				$grantResult = $this->passWordFlow($body);
				break;

			default:
				return $response->withJSON((object)[
					"Error" => "No valid grant type has been specified. Use 'grant_type'"
				],400);
				break;
		}

		return $response->withJSON($grantResult, 200);

	}


	private function passWordFlow($body){

		$checkClient = Client::where([
			["client_id", '=', $body['client_id']],
			["client_secret", '=', $body['client_secret']],
		])->first();

		if($checkClient){

			$findUser = User::where([
				["username", "=", $body['username']]
			])->first();

			if(!$findUser){
				return (object)[
					"Error" => "Username / Email not located"
				];
			}

			if(password_verify($body['password'], $findUser['password'])) {

				$checkScopes = $this->assignScope($body, $checkClient);

				if(!$checkScopes || !isset($body['scope'])){
					return (object)[
						"Error" => "Invalid Scope"
					];
				}

				// ALL IS GOOD, LETS MAKE A TOKEN!
				$saveToken = Token::create(array(
					"access_token" => $this->generateToken(),
					"client_id" => "fyndApp",
					"user_id" => $findUser->USER_ID,
					"expires" => gmdate("Y-m-d\TH:i:s\Z",time() + 3600),
					"scope" => $checkScopes
				));

				return $saveToken;

			}else{
				return (object)[
					"Error" => "Incorrect Password"
				];
			}
		}else{
			return (object)[
				"Error" => "Invalid Client ID / Secret Combination"
			];
		}

	}


	private function clientCredentialsFlow($body){

		$checkClient = Client::where([
			["client_id", '=', $body['client_id']],
			["client_secret", '=', $body['client_secret']],
		])->first();

		if($checkClient){

			$checkScopes = $this->assignScope($body, $checkClient);

			if(!$checkScopes){
				return (object)[
					"Error" => "Invalid Scope"
				];
			}

			// ALL IS GOOD, LETS MAKE A TOKEN!
			$saveToken = Token::create(array(
				"access_token" => $this->generateToken(),
				"client_id" => "fyndApp",
				"expires" => gmdate("Y-m-d\TH:i:s\Z",time() + 3600),
				"scope" => $checkScopes
			));

			return $saveToken;

		}else{
			return (object)[
				"Error" => "Invalid Client ID / Secret Combination"
			];
		}

	}


	public function generateToken(){

		if (function_exists('random_bytes')) {
			$randomData = random_bytes(20);
			if ($randomData !== false && strlen($randomData) === 20) {
				return bin2hex($randomData);
			}
		}
		if (function_exists('openssl_random_pseudo_bytes')) {
			$randomData = openssl_random_pseudo_bytes(20);
			if ($randomData !== false && strlen($randomData) === 20) {
				return bin2hex($randomData);
			}
		}
		if (function_exists('mcrypt_create_iv')) {
			$randomData = mcrypt_create_iv(20, MCRYPT_DEV_URANDOM);
			if ($randomData !== false && strlen($randomData) === 20) {
				return bin2hex($randomData);
			}
		}
		if (@file_exists('/dev/urandom')) { // Get 100 bytes of random data
			$randomData = file_get_contents('/dev/urandom', false, null, 0, 20);
			if ($randomData !== false && strlen($randomData) === 20) {
				return bin2hex($randomData);
			}
		}
		// Last resort which you probably should just get rid of:
		$randomData = mt_rand() . mt_rand() . mt_rand() . mt_rand() . microtime(true) . uniqid(mt_rand(), true);

		return substr(hash('sha512', $randomData), 0, 40);

	}

	private function assignScope($body, $client){

		$allowedScopes = explode($client->scope, ' ');

		$requestedScopes = explode($body['scope'], ' ');

		foreach($requestedScopes as $scope){
			if(!in_array($scope, $allowedScopes)){
				return false;
			}
		}

		return $body['scope'];

	}


}