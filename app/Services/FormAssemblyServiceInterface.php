<?php

namespace App\Services;

use App\Models\FormResponseArray;
use GuzzleHttp\Client;

interface FormAssemblyServiceInterface {
	public function getFormResponses(FormAssemblyClientServiceInterface $client, string $code, int $formId): FormResponseArray;
	public function getUser(FormAssemblyClientServiceInterface $client, string $code): object;
}
