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

	/**
	 * @OA\Get(
	 *     path="/api/v1/responses/{formId}/pdf",
	 *     operationId="responses/formId/pdf",
	 *     tags={"PDFReport"},
	 *     @OA\Parameter(
	 *         name="formId",
	 *         in="path",
	 *         description="The form ID",
	 *         required=true,
	 *         @OA\Schema(type="string")
	 *     ),
	 *     @OA\Response(
	 *         response="200",
	 *         description="Returns the PDF report.",
	 *         @OA\JsonContent()
	 *     ),
	 *     @OA\Response(
	 *         response="400",
	 *         description="Error: Bad request. When required parameters were not supplied.",
	 *     ),
	 * )
	 */
	public function getFormPdfResults(int $formId){
		return FileUtility::retrievePdf($formId);
	}

	/**
	 * @OA\Get(
	 *     path="/api/v1/responses/export/pdf/{formId}",
	 *     operationId="/responses/export/pdf/formId",
	 *     tags={"PDFGeneration"},
	 *     @OA\Parameter(
	 *         name="formId",
	 *         in="path",
	 *         description="The form ID",
	 *         required=true,
	 *         @OA\Schema(type="string")
	 *     ),
	 *     @OA\Parameter(
	 *         name="code",
	 *         in="header",
	 *         description="API authentication code",
	 *         required=true,
	 *         @OA\Schema(type="string")
	 *     ),
	 *     @OA\Response(
	 *         response="200",
	 *         description="Returns a standard message after firing off the PDF generation process.",
	 *         @OA\JsonContent()
	 *     ),
	 *     @OA\Response(
	 *         response="400",
	 *         description="Error: Bad request. When required parameters were not supplied.",
	 *     ),
	 * )
	 */
	public function generateFormPdfResults(int $formId, Request $request, FormAssemblyClientServiceInterface $client, FormAssemblyServiceInterface $form_assembly_service){
		$this->dispatch(new BuildPdfJob($request->header('code'), $formId, $client, $form_assembly_service));
		return response()->json(['message' => "Started PDF generation of Form Responses.  If an invalid code is passed, the responses PDF will not be generated."]);
	}


}
