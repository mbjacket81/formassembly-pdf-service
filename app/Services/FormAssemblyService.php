<?php

namespace App\Services;

use App\Http\Middleware\FormAssemblyMiddleware;
use App\Models\CustomException;
use App\Models\FormResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

class FormAssemblyService implements FormAssemblyServiceInterface  {
	public const OAUTH_URL = 'https://app.formassembly.com/oauth/access_token';
	const FORM_RESPONSES_EXPORT_URL = 'https://app.formassembly.com/api_v1/responses/export/';
	const USER_URL = 'https://app.formassembly.com/api_v1/users/profile.json';



	public function getFormResponses(FormAssemblyClientServiceInterface $client, string $code, int $formId) {
		try {
			$formResponses =
				$client->getClient($code)->request( 'GET',
				self::FORM_RESPONSES_EXPORT_URL . $formId . '.json');
			$jsonResponse      = json_decode( $formResponses->getBody() );
			$formResponseArray = [];
			if ( is_array( $jsonResponse->responses->response ) ) {
				foreach ( $jsonResponse->responses->response as $resp ) {
					array_push( $formResponseArray, new FormResponse( $resp ) );
				}
			}
			return $formResponseArray;
		}catch(RequestException $e){
			Log::error($e);
			if($e->getCode() == 422){
				throw new CustomException("Authentication error with FormAssembly", $e);
			}else{
				if($e instanceof ConnectException){
					throw new CustomException("There has been a networking error with FormAssembly", $e);
				}else{
					throw new CustomException("There has been an error communicating with FormAssembly", $e);
				}
			}
		}
	}

	public function getUser(FormAssemblyClientServiceInterface $client, string $code ) {
		try {
			$formResponses = $client->getClient($code)->request('GET', self::USER_URL);
			return json_decode($formResponses->getBody());
		} catch (RequestException $e) {
			Log::error($e);
			if($e->getCode() == 422){
				throw new CustomException("Authentication error with FormAssembly", $e);
			}else{
				if($e instanceof ConnectException){
					throw new CustomException("There has been a networking error with FormAssembly", $e);
				}else{
					throw new CustomException("There has been an error communicating with FormAssembly", $e);
				}
			}
		}
	}
}
