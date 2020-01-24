<?php


namespace App\Mail;


use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PdfNotificationMail extends Mailable {
	use Queueable, SerializesModels;

	public $navigationUrl = "http://localhost:8080";
	public $navigationButtonText = "";

	public $subject = "Your PDF Responses Report is ready";
	public $messageTitle = "";
	public $messageBody = "";
	public $noNavigation = false;

	public $sendToEmail = "noreply@formassembly.com";
	public $sendToName = "";
	public $fromEmail = null;
	public $fromName = "FormAssembly";
	public $attachFile = "";
	public $title = "";
	public $attachData = null;
	public $attachDataFilename = "";

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct($params){
		/**
		array('subject'=>'', 'messageTitle' => '', 'messageBody' => '', 'sendToEmail' => '', 'sendToName' => '', 'fromEmail' => '', 'fromName' => '',
		'navigationUrl' => '', 'navigationButtonText' => '', 'attachFile' => '')
		 **/
		if (is_array($params)) {
			if(isset($params["subject"])){
				$this->subject = $params["subject"];
			}
			if(isset($params["messageTitle"])){
				$this->messageTitle = $params["messageTitle"];
				$this->title = env('APP_NAME').' - '.$this->messageTitle;
			}
			if(isset($params["messageBody"])){
				$this->messageBody = $params["messageBody"];
			}
			if(isset($params["sendToEmail"])){
				$this->sendToEmail = $params["sendToEmail"];
			}
			if(isset($params["sendToName"])){
				$this->sendToName = $params["sendToName"];
			}
			if(isset($params["fromEmail"])){
				$this->fromEmail = $params["fromEmail"];
			}
			if(isset($params["fromName"])){
				$this->fromName = $params["fromName"];
			}
			if(isset($params["navigationUrl"])){
				$this->navigationUrl = $params["navigationUrl"];
			}
			if(isset($params["navigationButtonText"])){
				$this->navigationButtonText = $params["navigationButtonText"];
			}
			if(isset($params["attachFile"])){
				$this->attachFile = $params["attachFile"];
			}
			if(isset($params["noNavigation"])){
				$this->noNavigation = $params["noNavigation"];
			}
			if(isset($params["cc"])){
				$this->cc($params["cc"], $params["cc"]);
			}
			if(isset($params["attachData"])){
				$this->attachData = $params["attachData"];
			}
			if(isset($params["attachDataFilename"])){
				$this->attachDataFilename = $params["attachDataFilename"];
			}
		}
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build() {
		$msg = $this->from($this->fromEmail, $this->fromName);
		if (strpos($this->sendToEmail, ';') !== false) {
			$msg->to(explode(';',preg_replace('/\s+/', '', $this->sendToEmail)));
		}else {
			$msg->to( $this->sendToEmail, $this->sendToName );
		}
		if(!empty($this->attachFile)){
			$msg->attach($this->attachFile, array(
				'as' => 'report.pdf',
				'mime' => 'application/pdf'));
		}
		if(!empty($this->attachData)) {
			$msg->attachData( $this->attachData, ! empty( $this->attachDataFilename ) ? $this->attachDataFilename : "FileAttachment" );
		}
		return $msg->view('mail.pdf-notification');
	}

}
