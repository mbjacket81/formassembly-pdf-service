<?php


namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Services\FormAssemblyServiceInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;

class PdfController extends Controller {

	/**
	 * Create controller instance.
	 *
	 * @return void
	 */
	public function __construct() {

	}

	public function getFormPdfResults($formId, Request $request, FormAssemblyServiceInterface $form_assembly_service){
		//echo "TEST ${formId}";
//		return response()->json(["t"=>true]);//$request->has('code')]);//header('code');
//		echo $form_assembly_service->getFormResponses($request->header('code'), $formId);
//		return response()->json(['t'=>$form_assembly_service->getFormResponses($request->header('code'), $formId)]);
//		return response()->json($form_assembly_service->getFormResponses($request->header('code'), $formId));
		return $form_assembly_service->getFormResponses($request->header('code'), $formId);

//		return response()->json(['message' => "Started PDF generation of Form"]);// ${formId}"]);
	}

	private function getAuthenticatedFormAssemblyClient(Request $request){
//		$client = new \GuzzleHttp\Client();
//		$request->header('code');




	}

	public function getAccessToken(){
		$url = env('FA_URL')."/oauth/login?type=web&client_id=".env('FA_OAUTH_CLIENT_ID').
		       "&redirect_uri=RETURN_URL&response_type=code";
		return redirect('', 302);
	}

	private function generatePdf(){
		$pdfOptions = new Options();
		$pdfOptions->set('isRemoteEnabled', true);
		$dompdf = new Dompdf($pdfOptions);

		//$dompdf->loadHtml(ContractHelper::replaceContractTokens($contract));
//		$dompdf->render();
//		$pdfDoc = $dompdf->output();//$dompdf->stream($contractId.'.pdf',["Attachment"=>0]);
//		$uploadedFile = FileHelper::uploadContractDoc($pdfDoc, $contract->id.'.pdf', 'contract', $contract->id);
//		//Storage::disk('uploads')->put('contracts/' . $contract->id . '.pdf', $pdfDoc);
//		$contract->contractpdf = $uploadedFile->filename; //public_path() . '/uploads/contracts/' . $contract->id . '.pdf';
//		$contract->save();
	}
}
