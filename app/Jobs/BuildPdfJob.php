<?php

namespace App\Jobs;

use App\Http\Helpers\FileHelper;
use App\Services\FormAssemblyServiceInterface;

class BuildPdfJob extends Job {

	private $formAssemblyCode;
	private $formId;
	private $formAssemblyService;
	private $userEmail;

	/**
	 * Create job instance.
	 *
	 * @return void
	 */
	public function __construct($code, $formId, FormAssemblyServiceInterface $formAssemblyService) {
		$this->formAssemblyService = $formAssemblyService;
		$this->formAssemblyCode = $code;
		$this->formId = $formId;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle() {
		$responses = $this->formAssemblyService->getFormResponses($this->formAssemblyCode, $this->formId);
		$storeSucceeded = FileHelper::storePdf($this->formId, $responses);
		$userResponse = $this->formAssemblyService->getUser($this->formAssemblyCode);
		$this->userEmail = $userResponse->email;
		dispatch(new SendPdfNotificationEmail($this->formId, $this->userEmail, $storeSucceeded));
	}
}
