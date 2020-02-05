<?php

namespace App\Http\Controllers\v1;
use App\Services\mocks\MockFormAssemblyClientService;
use App\Services\FormAssemblyClientServiceInterface;
use App\Services\FormAssemblyService;
use \App\Services\FormAssemblyServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use TestCase;

class PdfControllerTest extends TestCase {

	/**
	 * testBuildingPdf
	 *
	 * @return void
	 */
	public function testMicroserviceCall() {
		$this->get( '/' );

		$this->assertEquals(
			$this->app->version(), $this->response->getContent()
		);
	}

}
