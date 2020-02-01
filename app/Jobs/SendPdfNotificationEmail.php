<?php

namespace App\Jobs;

use App\Mail\PdfNotificationMail;
use App\Models\FormResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class SendPdfNotificationEmail implements ShouldQueue {
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	private $formId;
	private $userEmail;
	private $pdfSucceeded;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct($formId, $userEmail, bool $pdfSucceeded) {
		$this->formId = $formId;
		$this->userEmail = $userEmail;
		$this->pdfSucceeded = $pdfSucceeded;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle() {
		$mailParams = [
			"subject" => "Form responses for Form #".$this->formId.($this->pdfSucceeded ? " Succeeded" : " Failed"),
			"messageTitle" => ($this->pdfSucceeded ? "Successfully" : "Unsuccessfully")." processed your form results.",
			"messageBody" => ($this->pdfSucceeded ?
				"Click here to find the results for <a href='".env("APP_URL")."/responses/".$this->formId."/pdf'>Form #".$this->formId."</a>." :
				"We are sorry to inform you, but we were unable to generate the form responses into PDF documents."
			),
			"fromEmail" => "noreply@formassembly.com",
			"sendToEmail" => $this->userEmail,
			"noNavigation" => !$this->pdfSucceeded,
			"navigationUrl" => env("APP_URL")."/api/v1/responses/".$this->formId."/pdf",
			"navigationButtonText" => "Results PDF",
		];
		$message = new PdfNotificationMail($mailParams);
		Mail::send($message);
	}
}
