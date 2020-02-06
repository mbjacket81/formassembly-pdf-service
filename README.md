<p align="center"><img src="https://www.formassembly.com/wp-content/uploads/2019/11/fa-logo@2x.png" width="400"></p>

[![Build Status](https://travis-ci.org/mbjacket81/formassembly-pdf-service.svg)](https://travis-ci.org/mbjacket81/formassembly-pdf-service)
[![Total Downloads](https://poser.pugx.org/mbjacket81/formassembly-pdf-service/d/total.svg)](https://packagist.org/packages/mbjacket81/formassembly-pdf-service)
[![Latest Stable Version](https://poser.pugx.org/mbjacket81/formassembly-pdf-service/v/stable.svg)](https://packagist.org/packages/mbjacket81/formassembly-pdf-service)
[![License](https://poser.pugx.org/mbjacket81/formassembly-pdf-service/license.svg)](https://packagist.org/packages/mbjacket81/formassembly-pdf-service)


## About Project

PHP microservice project that does the following:

- Handles incoming requests to generate a PDF version of each response for a particular form
- Processes the form data and creates a PDF file - feel free to choose any open source library that can generate PDF’s.
- Handles response back to the caller informing them that the PDF process has completed
- Sends an email to the form creator with a link to the location of the PDF documents
- Handles any errors and is able to reprocess a certain number of times - preferably configurable
- Provides test coverage as you see necessary to ensure that the functionality you build will not be regressed by future work

## PDF Generator structure

Laravel Lumen is being utilized to allow faster response times from the use of a more slimmed-down version of Laravel.

#### Packages Used

- guzzle:  to communicate with the FormAssembly API
- dompdf:  for generating PDF files
- (may not be able to use this with Lumen) vyuldashev/laravel-openapi:  to help in the generation of the Swagger OpenAPI docs]

Start locally: php -S localhost:8080 -t public/

## Specific Information

- Swagger API Documentation: http://localhost:8080/api/documentation
- Models generated from https://app.formassembly.com/api_v1/responses/export/{form_id}.json response object
- DomPDF utilized for the PDF generation with each response on a separate page
- Must acquire a "code" from FormAssembly using the following:
https://app.formassembly.com/oauth/authorize?type=web&client_id={CLIENT ID}&redirect_uri=http://localhost:8080&response_type=code
- To generate PDF of all responses for a form, must include the "code" parameter to: http://localhost:8080/api/v1/responses/export/pdf/{form id}
- Mail currently setup using Mailtrap.io in .env file (to prevent real outgoing emails)
- Emails that are sent will have a link to the localized report stored in storage/app/forms/{form id} folder
- utilized 2 FormAssembly API endpoints (get form responses and get user)
- Unit tests utilizing mocked FormAssembly API responses to verify model mapping functionality and exception cases

## Improvements

- Use external remote storage of PDF files rather than local storage


## Official Documentation

Documentation for the framework can be found on the [Lumen website](https://lumen.laravel.com/docs).

## License

The Lumen framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
