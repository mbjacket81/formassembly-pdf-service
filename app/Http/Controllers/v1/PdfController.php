<?php


namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\FileHelper;
use App\Jobs\BuildPdfJob;
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

	public function getFormPdfResults($formId){
		return FileHelper::retrievePdf($formId);
	}

	public function generateFormPdfResults($formId, Request $request, FormAssemblyServiceInterface $form_assembly_service){
		$this->dispatch(new BuildPdfJob($request->header('code'), $formId, $form_assembly_service));
		return response()->json(['message' => "Started PDF generation of Form Responses"]);
	}


}
