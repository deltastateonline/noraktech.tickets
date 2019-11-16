<?php namespace Console;
define("DS", DIRECTORY_SEPARATOR);
define("LOCALPATH", __DIR__.DS."..".DS );
define("EMAIL_PATH",LOCALPATH."emails".DS);


use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Style\SymfonyStyle;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;

use EmailboxProcessor\EmailReaderParser\Email_Reader ;
use EmailboxProcessor\Guid;
use Models\RecievedEmail;

use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

 

class ProcessMailboxCommand extends SymfonyCommand
{
	
	private $config = null;
	private $log = null;
	
	private $mailbox = "";

	public function configure()
	{
		$this->setName('mailbox')
		->setDescription('Process Imap Box.')
		->setHelp('This command allows Scraps an Imap Mail Box')
		->addArgument('port-type', InputArgument::REQUIRED, 'The Port Type is required.');
		
		$this->log = new Logger('Process.Mailbox');
		//$this->log->pushHandler(new StreamHandler('\logs\Process.Mailbox.log', Logger::INFO));
		$this->log->pushHandler(new StreamHandler('php://stderr', Logger::INFO));
		
		
		
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$type = $input->getArgument('port-type');
		$this->mailbox = sprintf("{%s}",$this->config["server-url"].$this->config["ports"][$type]);
		
		
		$this->log->info($type);		
		$this->log->info("Open Mailbox for reading");
		$this->log->info("Connection String : ".$this->mailbox);
	
		$repository = $this->init(); // initialize a  repository
		
		$messageIds = array();
		try {
			
			$loader = new FilesystemLoader('views');
			$twig = new Environment($loader);		
			
			$mailer = $this->init_swift();
			
			$reader = new Email_Reader($this->mailbox, $this->config["login-details"]["username"], $this->config["login-details"]["password"]);
			$messages = $reader->get_messages(2);
			
			foreach($messages as $amessage){
				$emailGuid = Guid::get_uuid();
				$folder = sprintf("%s%s",EMAIL_PATH,$emailGuid); // ends in a slash
			
				$this->log->info("Folder Created",array("folder"=>$folder));
				if (!mkdir($folder, 0777, true)) {
					throw new Exception(sprintf("Unable to create folder %s ", $folder));
				}
				$filename = sprintf("%s%semailcontent.html",$folder,DS);
				file_put_contents($filename, $amessage->html);
			
				$recievedEmail = new RecievedEmail();
			
				$recievedEmail->setGuid($emailGuid)
				->setEmailFrom($amessage->from)
				->setEmailTo($amessage->to)
				->setEmailCc($amessage->cc)
				->setReplyTo($amessage->reply_to)
				->setEmailSubject($amessage->subject)
				->setEmailDate($amessage->date)
				->setFromEmailaddress($amessage->from_email)
				->setEmailBody($filename);
			
				$amessage->attachments = array();
				if(!empty($amessage->attachments)){
					$attachments = array();
					foreach($amessage->attachments as $anAttachment){
						$newAttachmentDetails = array();
						$newAttachmentDetails["filepath"]= sprintf("%s%s%s",$folder,DS, $anAttachment["name"]);
						$filemoved = rename($anAttachment["path"], $newAttachmentDetails["filepath"]);
						$this->log->info($anAttachment["path"]." Moved to ".$newAttachmentDetails["filepath"]);
			
						unset($anAttachment["path"]);
						$attachments[] = array_merge($newAttachmentDetails, $anAttachment);
			
					}
						
					$recievedEmail->setAttachments(json_encode($attachments));
				}
			
				$repository->save($recievedEmail);
				
				$this->log->info("Ticket #".$recievedEmail->getId());
				
				if(!empty($recievedEmail->getId())){				
					
					$this->log->info("Sending a Reply to : ".$recievedEmail->getEmailSubject());
					$this->sendReply($recievedEmail, $mailer ,$twig);
				}
				
			}
			
			
			
			
		} catch (Exception $e) {	
			$this->log->error($e->getMessage());
		}
		$this->log->info("Processing Completed.");
		
	}
	
	public function setConfigs($config){	
		$this->config = $config;
	}
	
	private function init(){
		
		$mapper = new \ByJG\MicroOrm\Mapper(
				RecievedEmail::class,   // The full qualified name of the class
				'recieved_emails',        // The table that represents this entity
				'id'            // The primary key field
		);
		
		$databaseConnectionString = $this->config["database-connection"]["mysql"];
		$dataset = \ByJG\AnyDataset\Db\Factory::getDbRelationalInstance($databaseConnectionString);
		$repository = new \ByJG\MicroOrm\Repository($dataset, $mapper);
		
		return $repository;
	}
	
	private function init_swift(){
		
		
		$transport = (new Swift_SmtpTransport($this->config["smtp"]["hostname"], 25))
		->setUsername($this->config["smtp"]["username"])
		->setPassword($this->config["smtp"]["password"]);
		
		// Create the Mailer using your created Transport		
		return new Swift_Mailer($transport);
	}
	
	private function getEmailFromName($emailAddress = NULL){
		
		if(!empty($emailAddress)){
			$keywords = preg_split("/</", $emailAddress);
			return $keywords[0];
		}
		
		return "";
	}
	
	private function sendReply($recievedEmail , $mailer , $twig){
		
		try{
			$message = new Swift_Message();
			$originalfromName=  $this->getEmailFromName($recievedEmail->getEmailFrom());
			// Set a "subject"
				
			$subjectString = $twig->render('email-subject.twig', ['emailSubject' => $recievedEmail->getEmailSubject(),
					'ticketNumber' => '#'.$recievedEmail->getId()]);
				
				
			$bodyString = $twig->render('email-reply.twig', ['emailSubject' => $recievedEmail->getEmailSubject(),
					'emailForm' => $originalfromName]);
				
				
			$message->setSubject($subjectString);
				
			// Set the "From address"
			$message->setFrom([$this->config["smtp"]["sent-from"] => 'Support']);
		
			if(isset($this->config["smtp"]["send-emails-to"])){
				$message->addTo($this->config["smtp"]["send-emails-to"],$originalfromName);
			}else{
				$message->addTo($recievedEmail->getFromEmailaddress(),$originalfromName);
			}
				
			$message->setBody($bodyString);
			// Set a "Body"
			$message->addPart($bodyString, 'text/html');
				
			$result = $mailer->send($message);
		} catch (Exception $e) {
			$this->log->error("Sending Email ".$e->getMessage());
		}
	}
}