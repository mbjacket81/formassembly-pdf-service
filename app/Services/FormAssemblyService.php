<?php

namespace App\Services;

use App\Models\FormResponse;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

class FormAssemblyService implements FormAssemblyServiceInterface  {
	const OAUTH_URL = 'https://app.formassembly.com/oauth/access_token';
	const FORM_RESPONSES_EXPORT_URL = 'https://app.formassembly.com/api_v1/responses/export/';

	private function authenticate(string $code): string {
		$client = new \GuzzleHttp\Client();
		$authResponse = $client->request('POST', self::OAUTH_URL, [
			\GuzzleHttp\RequestOptions::FORM_PARAMS => [
				'code' => $code,
				'grant_type' => 'authorization_code',
				'type' => 'web_server',
				'client_id' => env('FA_OAUTH_CLIENT_ID'),
				'client_secret' => env('FA_OAUTH_CLIENT_SECRET'),
				'redirect_uri' => env('APP_URL')
			]
		]);
		$jsonResponse = json_decode($authResponse->getBody());
		if(!empty($jsonResponse) && !empty($authCode = $jsonResponse->{'access_token'})){
			return $authCode;
		}else{
			return null;
		}
	}

	public function getFormResponses(string $code, int $formId) {
		$client = new \GuzzleHttp\Client();
		$formResponses = $client->request('GET',
			self::FORM_RESPONSES_EXPORT_URL.$formId.'.json', [
			\GuzzleHttp\RequestOptions::HEADERS => [
				'Authorization' => 'Bearer '.$this->authenticate($code)
			]
		]);
		$jsonResponse = json_decode($formResponses->getBody());
		$formResponseArray = [];
		if(is_array($jsonResponse->responses->response)){
			foreach ($jsonResponse->responses->response as $resp){
				array_push($formResponseArray, new FormResponse($resp));
			}
		}
		return $formResponseArray;
	}
}
