<?php


namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\FileUtility;
use App\Jobs\BuildPdfJob;
use App\Models\FormResponse;
use App\Services\FormAssemblyClientServiceInterface;
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
	public function __construct() {}

	public function getFormPdfResults(int $formId){
		return FileUtility::retrievePdf($formId);
	}

	public function generateFormPdfResults(int $formId, Request $request, FormAssemblyClientServiceInterface $client, FormAssemblyServiceInterface $form_assembly_service){
		$this->dispatch(new BuildPdfJob($request->header('code'), $formId, $client, $form_assembly_service));
		return response()->json(['message' => "Started PDF generation of Form Responses.  If an invalid code is passed, the responses PDF will not be generated."]);
	}


}
