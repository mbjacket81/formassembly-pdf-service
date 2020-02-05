<?php


namespace App\Models;


use Dompdf\Dompdf;
use Dompdf\Options;

class FormResponseArray {
	public $formResponses;

	function __construct(FormResponse ...$responses) {
		$this->formResponses = $responses;
	}

	public function generateHtmlReport(): string{
		$html = "<style>.page_break { page-break-before: always; }</style>";
		$numResponses = count($this->formResponses);
		$i = 0;
		foreach ($this->formResponses as $response) {
			$html .= $response->generateHTMLReport();
			if(++$i != $numResponses) {
				$html .= "<div class='page_break'></div>";
			}
		}
		return $html;
	}

	public function generatePdf(){
		$pdfOptions = new Options();
		$dompdf = new Dompdf($pdfOptions);
		$dompdf->loadHtml($this->generateHtmlReport());
		$dompdf->render();
		return $dompdf->output();
	}

}
