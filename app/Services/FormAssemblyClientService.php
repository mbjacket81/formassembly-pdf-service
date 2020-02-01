<?php


namespace App\Services;


use App\Http\Middleware\FormAssemblyMiddleware;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;

class FormAssemblyClientService implements FormAssemblyClientServiceInterface {

	public function getClient($code): Client {
		$stack = new HandlerStack();
		$stack->setHandler(new CurlHandler());
		$stack->push( FormAssemblyMiddleware::add_auth($code));
		return new Client(['handler' => $stack]);
	}

}
