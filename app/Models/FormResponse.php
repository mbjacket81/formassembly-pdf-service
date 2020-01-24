<?php


namespace App\Models;


class FormResponse {
	public $formTitle;
	public $status;
	public $fieldSets = [];
	public $metaFields;

	function __construct($jsonResponseObject) {
		if(isset($jsonResponseObject->title)){
			$this->formTitle = self::getTextContent($jsonResponseObject->title);
		}
		if(isset($jsonResponseObject->status)){
			$this->status = $jsonResponseObject->status;
		}

		if(isset($jsonResponseObject->fieldset)){

			//process sections
			if(is_array($jsonResponseObject->fieldset)) {
				foreach ( $jsonResponseObject->fieldset as $fieldset ) {
					$localFieldset        = new FormResponseFieldset();
					$localFieldset->label = self::getTextContent( $fieldset->label );
					if ( isset( $fieldset->fieldset ) ) {
						self::processFieldsets( $localFieldset->fields, $fieldset->fieldset );
					}
					if ( isset( $fieldset->field ) ) {
						self::processFieldsets( $localFieldset->fields, $fieldset->field );
					}
					array_push( $this->fieldSets, $localFieldset );
				}
			}
			//meta fields
			if(isset($jsonResponseObject->field) && is_array($jsonResponseObject->field)){
				foreach ($jsonResponseObject->field as $field){
					$meta = new FormResponseField($field);
					$this->metaFields[$meta->label] = $meta->value;
				}
			}
		}
	}

	public static function getTextContent($obj){
		if(isset($obj) && isset($obj->textContent)){
			return $obj->textContent;
		}
	}

	private static function processFieldsets(&$array, $fields){
		if(isset($fields)) {
			if ( !is_array( $fields ) ) {
				if ( isset( $fields->field ) ) {
					if ( is_array( $fields->field ) ) {
						foreach ( $fields->field as $fld ) {
							array_push( $array, new FormResponseField( $fld ) );
						}
					} else {
						array_push( $array, new FormResponseField( $fields->field ) );
					}
				}
				if ( isset( $fields->fieldset ) ) {
					if ( is_array( $fields->fieldset ) ) {
						foreach ( $fields->fieldset as $fieldset ) {
							self::processFieldsets( $array, $fieldset );
						}
					} else if ( isset( $fields->fieldset ) ) {
						self::processFieldsets( $array, $fields->fieldset );
					}
				}
			} else {
				foreach ( $fields as $field ) {
					self::processFieldsets( $array, $field );
				}
			}
		}
	}
}
