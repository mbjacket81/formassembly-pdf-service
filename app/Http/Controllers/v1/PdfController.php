<?php


namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\FormResponse;
use App\Services\FormAssemblyServiceInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PdfController extends Controller {

	/**
	 * Create controller instance.
	 *
	 * @return void
	 */
	public function __construct() {

	}

	public function getFormPdfResults($formId, Request $request, FormAssemblyServiceInterface $form_assembly_service){
		$responses = $form_assembly_service->getFormResponses($request->header('code'), $formId);
		$this->generatePdf($formId, $responses);
		return response()->json(['message' => "Started PDF generation of Form Responses"]);
	}

	public function getAccessToken(){
		$url = env('FA_URL')."/oauth/login?type=web&client_id=".env('FA_OAUTH_CLIENT_ID').
		       "&redirect_uri=RETURN_URL&response_type=code";
		return redirect('', 302);
	}

	private function generateHtmlReport($responses){
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
					$html .= "<tr><th>{$field->label}</th><td>{$field->value}</td></tr>";
				}
				$html .= "</table>";
			}
			if(++$i != $numResponses) {
				$html .= "<div class='page_break'></div>";
			}
		}
		return $html;
	}

	private function generatePdf($formId, $responses){
		$pdfOptions = new Options();
		//$pdfOptions->set('isRemoteEnabled', true);
		$dompdf = new Dompdf($pdfOptions);
		$dompdf->loadHtml($this->generateHtmlReport($responses));
		$dompdf->render();
		$pdf = $dompdf->output();
		$uploadedFile = Storage::disk('local')->put("forms/{$formId}.pdf", $pdf);
//		$url = $uploadedFile->filename; //public_path() . "/uploads/forms/{$formId}.pdf";
	}
}
