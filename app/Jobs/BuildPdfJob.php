<?php

namespace App\Jobs;

use App\Http\Helpers\FileUtility;
use App\Models\CustomException;
use App\Services\FormAssemblyClientServiceInterface;
use App\Services\FormAssemblyServiceInterface;

class BuildPdfJob extends Job {

	private $formAssemblyCode;
	private $formId;
	private $formAssemblyService;
	private $formAssemblyClientService;
	private $userEmail;

	/**
	 * Create job instance.
	 *
	 * @return void
	 */
	public function __construct($code, $formId, FormAssemblyClientServiceInterface $client, FormAssemblyServiceInterface $formAssemblyService) {
		$this->formAssemblyService = $formAssemblyService;
		$this->formAssemblyCode = $code;
		$this->formId = $formId;
		$this->formAssemblyClientService = $client;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle() {
		try {
			$responses       = $this->formAssemblyService->getFormResponses($this->formAssemblyClientService, $this->formAssemblyCode, $this->formId );
			$storeSucceeded  = FileUtility::storePdf( $this->formId, false, $responses );
			$userResponse    = $this->formAssemblyService->getUser($this->formAssemblyClientService, $this->formAssemblyCode );
			$this->userEmail = $userResponse->email;
			dispatch( new SendPdfNotificationEmail( $this->formId, $this->userEmail, $storeSucceeded ) );
		}catch(\Exception $ex){
			if(isset($this->userEmail)){
				dispatch( new SendPdfNotificationEmail( $this->formId, $this->userEmail, false ) );
			}
		}
	}
}
