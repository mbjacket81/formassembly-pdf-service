<?php


namespace App\Http\Helpers;


use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class FileHelper {

	public static function retrievePdf($formId){
		$files = Storage::disk( 'local' )->files( "forms/{$formId}" );
		if(count($files) > 0){
			$file = File::get(storage_path('app/'.$files[0]));
			$type = File::mimeType(storage_path('app/'.$files[0]));
			$response = Response::make($file, 200);
			$response->header("Content-Type", $type);
			return $response;
		}else{
			return null;
		}
	}

	public static function storePdf($formId, $responses, $forceGenerate = false){
		if(!$forceGenerate) {
			$files = Storage::disk( 'local' )->files( "forms/{$formId}" );
			if(count($files) < 1) { //no files, store pdf
				return FileHelper::generatePdf($formId, $responses);
			}else{
				return true;
			}
		}else{
			$files = Storage::disk( 'local' )->files( "forms/{$formId}" );
			if(count($files) < 1) { //no files, store pdf
				return FileHelper::generatePdf($formId, $responses);
			}else{
				$lastFile = $files[0];
				$lastFilePathArray = explode(".",$lastFile);
				$version = $lastFilePathArray[count($lastFilePathArray)-2];
				return FileHelper::generatePdf($formId, $responses, $version);
			}
		}
	}

	private static function generatePdf($formId, $responses, $version = 1){
		$pdfOptions = new Options();
		$dompdf = new Dompdf($pdfOptions);
		$dompdf->loadHtml(FileHelper::generateHtmlReport($responses));
		$dompdf->render();
		$pdf = $dompdf->output();
		Storage::disk('local')->put("forms/{$formId}/{$formId}.{$version}.pdf", $pdf);
		return Storage::disk('local')->exists("forms/{$formId}/{$formId}.{$version}.pdf");
	}

	//created in case individual responses are needed
	private static function generateIndividualResponsePdfs($formId, $responses){
		foreach ($responses as $response) {
			$pdfOptions = new Options();
			$dompdf     = new Dompdf( $pdfOptions );
			$dompdf->loadHtml( FileHelper::generateIndividualResponseHtmlReport( $response ) );
			$dompdf->render();
			$pdf = $dompdf->output();
			Storage::disk( 'local' )->put( "forms/{$formId}/responses/{$response->metaFields->response_id}.pdf", $pdf );
		}
	}

	private static function generateHtmlReport($responses){
		$html = "<style>.page_break { page-break-before: always; }</style>";
		$numResponses = count($responses);
		$i = 0;
		foreach ($responses as $response) {
			$html .= "<small>RESPONSE #".$response->metaFields['response_id'].
			         (isset($response->metaFields['date_submitted']) ? (" - SUBMITTED ON ".$response->metaFields['date_submitted']) : "")."</small><br/>";
			$html .= "<h1>{$response->formTitle}</h1>";
			foreach ( $response->fieldSets as $field_set ) {
				$html .= "<h3>{$field_set->label}</h3><hr>";
				$html .= "<table>";
				foreach ( $field_set->fields as $field ) {
					$html .= "<tr><th>{$field->label}</th>";
					if(empty($field->value)){
						$html .= "<td style='color:red'><i>No answer given.</i></td>";
					}else{
						$html .= "<td>{$field->value}</td>";
					}
					$html .= "</tr>";
				}
				$html .= "</table>";
			}
			if(++$i != $numResponses) {
				$html .= "<div class='page_break'></div>";
			}
		}
		return $html;
	}

	//created in case individual responses are needed
	private static function generateIndividualResponseHtmlReport($response){
		$html = "<small>RESPONSE #".$response->metaFields['response_id'].
		         (isset($response->metaFields['date_submitted']) ? (" - SUBMITTED ON ".$response->metaFields['date_submitted']) : "")."</small><br/>";
		$html .= "<h1>{$response->formTitle}</h1>";
		foreach ( $response->fieldSets as $field_set ) {
			$html .= "<h3>{$field_set->label}</h3><hr>";
			$html .= "<table>";
			foreach ( $field_set->fields as $field ) {
				$html .= "<tr><th>{$field->label}</th>";
				if(empty($field->value)){
					$html .= "<td style='color:red'><i>No answer given.</i></td>";
				}else{
					$html .= "<td>{$field->value}</td>";
				}
				$html .= "</tr>";
			}
			$html .= "</table>";
		}
		return $html;
	}

}
