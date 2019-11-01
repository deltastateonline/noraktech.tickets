<?php
namespace Ticketing\Models;

class RecievedEmail{
	
	private $guid 	  = null;
	private $from     = null;
	private $to       = null;
	private $cc       = null;
	private $reply_to = null;
	private $subject  = null;
	private $date     = null;
	
	private $created = null;
	
	private $from_email = null;
	
	private $body = null;
	
	private $attachments = null;
	
	private $response_sent = null;
	
	
	public function __construct(){
		$this->response_sent = false;
		$this->created = date("Y-m-d H:i:s");
	}
	
	public function setGuid($value){
		$this->guid  = $value;
		return $this;		
	}
	
	public function setFrom($value){
		$this->from  = $value;
		return $this;
	}
	
	public function setTo($value){
		$this->to  = $value;
		return $this;
	}
	
	public function setCc($value){
		$this->cc  = $value;
		return $this;
	}
	
	public function setReplyTo($value){
		$this->reply_to  = $value;
		return $this;
	}
	
	public function setSubject($value){
		$this->subject  = $value;
		return $this;
	}
	public function setDate($value){
		$this->date  = $value;
		return $this;
	}
	public function setFromEmail($value){
		$this->from_email  = $value;
		return $this;
	}
	
	public function setBody($value){
		$this->body  = $value;
		return $this;
	}
	
	public function setAttachments($value){
		$this->attachments  = $value;
		return $this;
	}
}