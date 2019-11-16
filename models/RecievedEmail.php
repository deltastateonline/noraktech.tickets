<?php
namespace Models;

class RecievedEmail{
	
	private $id= null;
	private $guid 	  = null;
	private $emailfrom     = null;
	private $emailto       = null;
	private $emailcc       = null;
	private $replyto = null;
	private $emailsubject  = null;	
	private $fromemailaddress = null;
		
	private $emailbody = null;	
	private $attachments = null;	
	private $responsesent = null;
	
	private $created = null;
	private $emaildate     = null;
	
	
	public function __construct(){
		$this->responsesent = false;
		$this->created = date("Y-m-d H:i:s");
	}
	
	public function setId($value){
		$this->id = $value;
		return $this;
	}
	public function setGuid($value){
		$this->guid  = $value;
		return $this;		
	}
	
	public function setEmailFrom($value){
		$this->emailfrom  = $value;
		return $this;
	}
	
	public function setEmailTo($value){
		$this->emailto  = $value;
		return $this;
	}
	
	public function setEmailCc($value){
		$this->emailcc  = $value;
		return $this;
	}
	
	public function setReplyTo($value){
		$this->replyto  = $value;
		return $this;
	}
	
	public function setEmailSubject($value){
		$this->emailsubject  = $value;
		return $this;
	}
	public function setEmailDate($value){
		$this->emaildate  = $value;
		return $this;
	}
	public function setFromEmailaddress($value){
		$this->fromemailaddress  = $value;
		return $this;
	}
	
	public function setEmailBody($value){
		$this->emailbody  = $value;
		return $this;
	}
	
	public function setAttachments($value){
		$this->attachments  = $value;
		return $this;
	}
	
	
	public function getGuid(){
		return $this->guid;
	}
	
	public function getEmailFrom(){
		return $this->emailfrom ;
	}
	
	public function getEmailTo(){
		return $this->emailto ;
	}
	
	public function getEmailCc(){
		return $this->emailcc ;
	}
	
	public function getReplyTo(){
		return $this->replyto ;
	}
	
	public function getEmailSubject(){
		return $this->emailsubject ;
	}
	public function getEmailDate(){
		return $this->emaildate ;
	}
	public function getFromEmailaddress(){
		return $this->fromemailaddress ;
	}
	
	public function getEmailBody(){
		return $this->emailbody ;
	}
	
	public function getAttachments(){
		return $this->attachments ;
	}
	
	public function getCreated(){
		return $this->created;
	}
	
	public function getId(){
		return $this->id;
	}
}