<?php
define("DS", DIRECTORY_SEPARATOR);
define("LOCALPATH", __DIR__.DS."..".DS );
define("EMAIL_PATH",LOCALPATH."emails".DS);

require_once LOCALPATH."vendor".DS."autoload.php";
require_once(LOCALPATH."configs".DS."config.php");

$type = "nossl";

$mailbox = sprintf("{%s}",$config["server-url"].$config["ports"][$type]);

logging("Open Mailbox for reading");
try {
	$reader = new EmailboxProcessor\EmailReaderParser\Email_Reader($mailbox, $config["login-details"]["username"], $config["login-details"]["password"]);	
	$messages = $reader->get_messages(5);	
	 
	foreach($messages as $amessage){
		$emailGuid = EmailboxProcessor\Guid::get_uuid();
		$folder = sprintf("%s%s",EMAIL_PATH,$emailGuid); // ends in a slash
		
		logging("Folder Created :".$folder);
		if (!mkdir($folder, 0777, true)) {
			throw new Exception(sprintf("Unable to create folder %s ", $folder));
		}
		$filename = sprintf("%s%semailcontent.html",$folder,DS);
		file_put_contents($filename, $amessage->html);	
		
		$recievedEmail = new Models\RecievedEmail();
		
		$recievedEmail->setGuid($emailGuid)
		->setFrom($amessage->from)
		->setTo($amessage->to)
		->setCc($amessage->cc)
		->setReplyTo($amessage->reply_to)
		->setSubject($amessage->subject)
		->setDate($amessage->date)
		->setFromEmail($amessage->from_email)
		->setBody($filename);

		if(!empty($amessage->attachments)){
			$attachments = array();
			foreach($amessage->attachments as $anAttachment){
				$newAttachmentDetails = array();
				$newAttachmentDetails["filepath"]= sprintf("%s%s%s",$folder,DS, $anAttachment["name"]);
				$filemoved = rename($anAttachment["path"], $newAttachmentDetails["filepath"]);
				logging($anAttachment["path"]." Moved to ".$newAttachmentDetails["filepath"]);
				
				unset($anAttachment["path"]);
				$attachments[] = array_merge($newAttachmentDetails, $anAttachment);
				
			}
			
			$recievedEmail->setAttachments(json_encode($attachments));
		}

		print_r($recievedEmail);
	} 
	
	//$unread = $reader->get_unread();
	
	//print_r($messages);
} catch (Exception $e) {	
	print_r($e);	
}
logging("Processing Completed.");

//echo "</pre>";

?>