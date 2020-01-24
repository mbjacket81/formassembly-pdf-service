<?php

namespace App\Http\Controllers\v1;
use \App\Services\FormAssemblyServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PdfControllerTest extends TestCase {

	/**
	 * testBuildingPdf
	 *
	 * @return void
	 */
	public function testBuildingPdf() {
		$this->get( '/' );

		$this->assertEquals(
			$this->app->version(), $this->response->getContent()
		);
	}

	public function testFormResponses(FormAssemblyServiceInterface $form_assembly_service) {
		$form_assembly_service->getFormResponses('', 1);
		return new \Illuminate\Support\Facades\Response();
	}
}
