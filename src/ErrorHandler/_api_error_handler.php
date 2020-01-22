<?php

namespace App\ErrorHandler;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class _api_error_handler
{
	public function __invoke(Request $request, Response $response, \Exception $exception)
	{
		$status = $exception->getCode() ?: 500;
		$data = [
			"Result" => "Error",
			"Error" => $exception->getMessage(),
		];
		$body = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
		return $response
			->withStatus(500)
			->write($body);
	}
}