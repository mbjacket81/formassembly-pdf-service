<?php

namespace App\Services;

use GuzzleHttp\Client;

interface FormAssemblyServiceInterface {
	public function getFormResponses(FormAssemblyClientServiceInterface $client, string $code, int $formId);
	public function getUser(FormAssemblyClientServiceInterface $client, string $code);
}
