<?php
define("DS", DIRECTORY_SEPARATOR);
define("LOCALPATH", __DIR__.DS."..".DS );
define("EMAIL_PATH",LOCALPATH."emails".DS);

require_once LOCALPATH."vendor".DS."autoload.php";
require_once(LOCALPATH."configs".DS."config.php");

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;

$log = new Logger('Process.Mailbox');
//$log->pushHandler(new StreamHandler('\logs\Process.Mailbox.log', Logger::INFO));
$log->pushHandler(new StreamHandler('php://stderr', Logger::INFO));

$type = "nossl";
$mailbox = sprintf("{%s}",$config["server-url"].$config["ports"][$type]);

$mapper = new \ByJG\MicroOrm\Mapper(
		Models\RecievedEmail::class,   // The full qualified name of the class
		'recieved_emails',        // The table that represents this entity
		'id'            // The primary key field
);

$databaseConnectionString = $config["database-connection"]["mysql"];
$dataset = ByJG\AnyDataset\Db\Factory::getDbRelationalInstance($databaseConnectionString);
$repository = new \ByJG\MicroOrm\Repository($dataset, $mapper);

$log->info("Open Mailbox for reading");

$messageIds = array();
try {
	$reader = new EmailboxProcessor\EmailReaderParser\Email_Reader($mailbox, $config["login-details"]["username"], $config["login-details"]["password"]);	
	$messages = $reader->get_messages(5);	
	 
	foreach($messages as $amessage){
		$emailGuid = EmailboxProcessor\Guid::get_uuid();
		$folder = sprintf("%s%s",EMAIL_PATH,$emailGuid); // ends in a slash
		
		$log->info("Folder Created",array("folder"=>$folder));
		if (!mkdir($folder, 0777, true)) {
			throw new Exception(sprintf("Unable to create folder %s ", $folder));
		}
		$filename = sprintf("%s%semailcontent.html",$folder,DS);
		file_put_contents($filename, $amessage->html);	
		
		$recievedEmail = new Models\RecievedEmail();
		
		$recievedEmail->setGuid($emailGuid)
		->setEmailFrom($amessage->from)
		->setEmailTo($amessage->to)
		->setEmailCc($amessage->cc)
		->setReplyTo($amessage->reply_to)
		->setEmailSubject($amessage->subject)
		->setEmailDate($amessage->date)
		->setFromEmailaddress($amessage->from_email)
		->setEmailBody($filename);

		if(!empty($amessage->attachments)){
			$attachments = array();
			foreach($amessage->attachments as $anAttachment){
				$newAttachmentDetails = array();
				$newAttachmentDetails["filepath"]= sprintf("%s%s%s",$folder,DS, $anAttachment["name"]);
				$filemoved = rename($anAttachment["path"], $newAttachmentDetails["filepath"]);
				$log->info($anAttachment["path"]." Moved to ".$newAttachmentDetails["filepath"]);
				
				unset($anAttachment["path"]);
				$attachments[] = array_merge($newAttachmentDetails, $anAttachment);
				
			}
			
			$recievedEmail->setAttachments(json_encode($attachments));
		}

		$repository->save($recievedEmail);
	} 
	
	/*
	if(is_array($messages)){
		$messageIds = array_keys($messages);
		$folder = "inbox.emailprocessed";
		for($i=0; $i < count($messageIds); $i++){
			$log->info("Mail Moved.", array("subject"=>$messages[$messageIds[$i]]->subject));
			$reader->move($messageIds[$i], $folder,TRUE);
		}
	}*/
	
} catch (Exception $e) {	
	print_r($e);	
}
$log->info("Processing Completed.");

//echo "</pre>";

?>