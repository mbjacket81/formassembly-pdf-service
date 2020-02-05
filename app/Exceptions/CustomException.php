<?php


namespace App\Exceptions;

use Illuminate\Support\Facades\Log;

class CustomException extends \Exception {
	public $error;

	public function __construct($errorMessage, $error = null) {
		$this->message = $errorMessage;
		$this->error = $error;
		if(env('APP_DEBUG')) {
			Log::debug( "Error thrown:  " . $errorMessage );
		}
	}

}
