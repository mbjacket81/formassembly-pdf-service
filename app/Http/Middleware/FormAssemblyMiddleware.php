<?php


namespace App\Http\Middleware;


use App\Models\CustomException;
use App\Services\FormAssemblyService;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\CurlHandler;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\RequestInterface;

class FormAssemblyMiddleware {

	public static function add_auth($code){
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
					if ( ! empty( $jsonResponse ) && ! empty( $authCode = $jsonResponse->{'access_token'} ) ) {
						$request = $request->withHeader('Authorization', 'Bearer ' . $authCode);
					}
				}catch(RequestException $e){
					Log::error($e);
					throw new CustomException("There has been an error communicating with FormAssembly", $e);
				}
				return $handler($request, $options);
			};
		};


	}
}
