<?php


namespace App\Http\Middleware;

use App\Exceptions\ConnectionException;
use App\Services\FormAssemblyService;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\CurlHandler;
use Illuminate\Auth\AuthenticationException;
use Psr\Http\Message\RequestInterface;

class FormAssemblyMiddleware {

	public static function add_auth(string $code){
		return function (callable $handler) use ($code) {
			return function (
				RequestInterface $request,
				array $options
			) use ($handler, $code) {
				try {
					$client       = new \GuzzleHttp\Client();
					$authResponse = $client->request( 'POST', FormAssemblyService::OAUTH_URL, [
						\GuzzleHttp\RequestOptions::FORM_PARAMS => [
							'code'          => $code,
							'grant_type'    => 'authorization_code',
							'type'          => 'web_server',
							'client_id'     => env( 'FA_OAUTH_CLIENT_ID' ),
							'client_secret' => env( 'FA_OAUTH_CLIENT_SECRET' ),
							'redirect_uri'  => env( 'APP_URL' )
						]
					] );
					$jsonResponse = json_decode( $authResponse->getBody() );
					if ( !empty( $jsonResponse )){
						if(!empty( $authCode = $jsonResponse->{'access_token'} ) ) {
							$request = $request->withHeader( 'Authorization', 'Bearer ' . $authCode );
						}
					}
					if(!isset($authCode) || empty($authCode)){
						throw new AuthenticationException("Access token could not be attained with the given code parameter.");
					}
				}catch(RequestException $e){
					if($e->getCode() == 400){
						$jsonResponse = json_decode( $e->getResponse()->getBody() );
						if ( !empty( $jsonResponse ) && strcmp($jsonResponse->error, "invalid_client") == 0){
							throw new AuthenticationException("The code given is invalid and we cannot login.");
						}
						throw new ConnectionException("A Bad Request was made with FormAssembly, check the code parameter given.");
					}
					throw new ConnectionException("There has been an error communicating with FormAssembly", $e);
				}
				return $handler($request, $options);
			};
		};


	}
}
