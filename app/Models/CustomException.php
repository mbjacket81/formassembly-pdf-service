<?php


namespace App\Models;


class CustomException extends \Exception {
	public $error;

	public function __construct($errorMessage, $error) {
		$this->message = $errorMessage;
		$this->error = $error;
	}

}
