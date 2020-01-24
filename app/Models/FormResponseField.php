<?php


namespace App\Models;


class FormResponseField {
	public $label;
	public $value;
	public $type;

	public function __construct($fieldObj){
		$this->label = isset($fieldObj->label) ? FormResponse::getTextContent($fieldObj->label) : null;
		$this->value = isset($fieldObj->value) ? FormResponse::getTextContent($fieldObj->value) : null;
		$this->type = isset($fieldObj->type) ? $fieldObj->type : null;
	}
}
