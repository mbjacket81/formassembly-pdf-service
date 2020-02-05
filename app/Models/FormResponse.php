<?php


namespace App\Models;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;

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

	public function generateHTMLReport(): string{
		$html = "<small>RESPONSE ".
		        (isset($this->metaFields['response_id']) ? "#".$this->metaFields['response_id'] : "") .
		        (isset($response->metaFields['date_submitted']) ? (" - SUBMITTED ON ".$this->metaFields['date_submitted']) : "").
		        "</small><br/>";
		if(!empty($this->formTitle)) {
			$html .= "<h1>{$this->formTitle}</h1>";
		}
		if(!empty($this->fieldSets)) {
			foreach ( $this->fieldSets as $field_set ) {
				$html .= "<h3>{$field_set->label}</h3><hr>";
				$html .= "<table>";
				foreach ( $field_set->fields as $field ) {
					$html .= "<tr><th>{$field->label}</th>";
					if ( empty( $field->value ) ) {
						$html .= "<td style='color:red'><i>No answer given.</i></td>";
					} else {
						$html .= "<td>{$field->value}</td>";
					}
					$html .= "</tr>";
				}
				$html .= "</table>";
			}
		}
		return $html;
	}

	public function generateResponsePdf(): string {
		$pdfOptions = new Options();
		$dompdf     = new Dompdf( $pdfOptions );
		$dompdf->loadHtml( $this->generateHTMLReport() );
		$dompdf->render();
		return $dompdf->output();
	}
}
