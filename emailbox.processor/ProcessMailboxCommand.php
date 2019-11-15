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
		->addArgument('serverkey', InputArgument::REQUIRED, 'The server key is required.');
		
		$this->log = new Logger('Process.Mailbox');
		//$this->log->pushHandler(new StreamHandler('\logs\Process.Mailbox.log', Logger::INFO));
		$this->log->pushHandler(new StreamHandler('php://stderr', Logger::INFO));
		
		
		
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$type = "nossl1";
		$this->mailbox = sprintf("{%s}",$this->config["server-url"].$this->config["ports"][$type]);
		
		$serverKey = $input->getArgument('serverkey');
		$this->log->info($serverKey);		
		$this->log->info("Open Mailbox for reading");
		$this->log->info("Connection String : ".$this->mailbox);
		
		$repository = $this->init(); // initialize a  repository
		
		$messageIds = array();
		try {
			$reader = new Email_Reader($this->mailbox, $this->config["login-details"]["username"], $this->config["login-details"]["password"]);
			$messages = $reader->get_messages(5);
			
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
}