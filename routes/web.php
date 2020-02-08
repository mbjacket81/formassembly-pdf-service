<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

Route::get('/', function () {
    return Route::app()->version();
});

Route::group(['prefix' => 'api'], function() {
	Route::group(['prefix' => 'v1'], function() {
		Route::get('responses/export/pdf/immediate/{formId}', 'v1\PdfController@generateResultsPdfImmediately');
		Route::get('responses/export/pdf/{formId}', 'v1\PdfController@generateFormPdfResults');
		Route::get('responses/{formId}/pdf', ['as' => 'directFile', 'uses' => 'v1\PdfController@getFormPdfResults']);
	});
});
