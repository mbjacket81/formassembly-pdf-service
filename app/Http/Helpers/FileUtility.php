<?php


namespace App\Http\Helpers;


use App\Models\FormResponse;
use App\Models\FormResponseArray;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class FileUtility {

	public static function retrievePdf(int $formId): \Illuminate\Http\Response {
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

	public static function storePdf(int $formId, bool $forceGenerate = false, FormResponseArray $formResponses): bool{
		$files = Storage::disk( 'local' )->files( "forms/{$formId}" );
		if(count($files) < 1) { //no files, store pdf
			return FileUtility::generatePdf($formId, $formResponses);
		}
		if($forceGenerate) {
			$lastFile = $files[0];
			$lastFilePathArray = explode(".",$lastFile);
			$version = $lastFilePathArray[count($lastFilePathArray)-2];
			return FileUtility::generatePdf($formId, $formResponses, $version);
		}
		return true; //already stored the PDF
	}

	private static function generatePdf(int $formId, FormResponseArray $formResponses, int $version = 1): bool{
		$pdf = $formResponses->generatePdf();
		Storage::disk('local')->put("forms/{$formId}/{$formId}.{$version}.pdf", $pdf);
		return Storage::disk('local')->exists("forms/{$formId}/{$formId}.{$version}.pdf");
	}

	//created in case individual responses are needed
	private static function generateIndividualResponsePdfs($formId, FormResponseArray $formResponses): bool{
		foreach ($formResponses->formResponses as $response) {
			$pdf = $response->generateResponsePdf();
			Storage::disk( 'local' )->put( "forms/{$formId}/responses/{$response->metaFields->response_id}.pdf", $pdf );
		}
	}

}
