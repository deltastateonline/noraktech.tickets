<?php
namespace Models;

class RecievedEmail{
	
	private $guid 	  = null;
	private $email_from     = null;
	private $email_to       = null;
	private $email_cc       = null;
	private $reply_to = null;
	private $email_subject  = null;
	private $email_date     = null;
	
	private $created = null;
	
	private $from_emailaddress = null;
	
	private $email_body = null;
	
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
		$this->email_from  = $value;
		return $this;
	}
	
	public function setTo($value){
		$this->email_to  = $value;
		return $this;
	}
	
	public function setCc($value){
		$this->email_cc  = $value;
		return $this;
	}
	
	public function setReplyTo($value){
		$this->reply_to  = $value;
		return $this;
	}
	
	public function setSubject($value){
		$this->email_subject  = $value;
		return $this;
	}
	public function setDate($value){
		$this->email_date  = $value;
		return $this;
	}
	public function setFromEmail($value){
		$this->from_email  = $value;
		return $this;
	}
	
	public function setBody($value){
		$this->email_body  = $value;
		return $this;
	}
	
	public function setAttachments($value){
		$this->attachments  = $value;
		return $this;
	}
	
	
	public function getGuid(){
		return $this->guid;
	}
	
	public function getBody(){
		return $this->email_body;
	}
}