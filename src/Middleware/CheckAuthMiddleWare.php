<?php

namespace App\MiddleWare;

use App\Utils\APIRateLimiter;
use App\Models\Auth\oAuthAccessToken as Token;

class CheckAuthMiddleWare {

	public function __construct($container) {

		$tokenDB = $container->get('db')->table("oauth_access_tokens");

	}

	public function __invoke($request, $response, $next) {

		if($request->hasHeader('Authorization')){

			$token = $this->getTokenFromHeader($request);

			$checkToken = Token::where([
				["access_token", "=", $token]
			])->first();

			if($checkToken){

				if($this->checkTokenExpiry($checkToken)){
					$request = $request->withAttribute('loggedInUser', $checkToken->user_id);

					return $next($request, $response);
				}else{
					return $response->withJSON((object)[
						"Error" => "The provided token has expired. Please acquire a new token"
					]);
				}

			}else{
				return $response->withJSON((object)[
					"Error" => "The provided token cannot be found or is invalid."
				]);
			}

		}else{
			return $response->withJSON((object)[
				"Error" => "You have not included the authorization header with your request."
			]);
		}


	}

	public function getTokenFromHeader($request){
		$header = $request->getHeader('Authorization');
		$token = str_replace("Bearer ", "", $header);
		return $token;
	}

	public function checkTokenExpiry($token){

		return strtotime($token->expires) > strtotime('now');

	}

}