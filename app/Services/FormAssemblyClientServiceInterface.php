<?php


namespace App\Services;


use GuzzleHttp\Client;

interface FormAssemblyClientServiceInterface {
	public function getClient($code): Client;
}
