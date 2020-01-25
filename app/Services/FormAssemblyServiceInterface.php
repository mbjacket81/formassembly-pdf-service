<?php

namespace App\Services;

interface FormAssemblyServiceInterface {
	public function getFormResponses(string $code, int $formId);
	public function getUser(string $code);
}
