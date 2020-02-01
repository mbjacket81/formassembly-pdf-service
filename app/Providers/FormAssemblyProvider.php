<?php


namespace App\Providers;


use Illuminate\Support\ServiceProvider;

class FormAssemblyProvider extends ServiceProvider {

	public function boot() {
		//
	}

	public function register() {
		$this->app->bind(\App\Services\FormAssemblyServiceInterface::class, function() {
			return new \App\Services\FormAssemblyService();
		});
		$this->app->bind(\App\Services\FormAssemblyClientServiceInterface::class, function() {
			return new \App\Services\FormAssemblyClientService();
		});
	}

}
