<?php

define("DS", DIRECTORY_SEPARATOR);
define("LOCALPATH", __DIR__.DS."..".DS );
define("EMAIL_PATH",LOCALPATH."emails".DS);

require_once LOCALPATH."vendor".DS."autoload.php";
require_once(LOCALPATH."configs".DS."config.php");


use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;

// create a log channel
$log = new Logger('test.orm');
//$log->pushHandler(new StreamHandler('F:\logs\your.log', Logger::WARNING));
$log->pushHandler(new StreamHandler('php://stderr', Logger::WARNING));

// add records to the log
$log->warning('Foo', array("Here","again"));
$log->error('Bar');




$mapper = new \ByJG\MicroOrm\Mapper(
		Models\RecievedEmail::class,   // The full qualified name of the class
		'recieved_emails',        // The table that represents this entity
		'id'            // The primary key field
);

$dataset = ByJG\AnyDataset\Db\Factory::getDbRelationalInstance('sqlite:db/recievedemails.db');
$repository = new \ByJG\MicroOrm\Repository($dataset, $mapper);

$newEmail = new Models\RecievedEmail();
 $newEmail->setAttachments("Test attachments")
	->setEmailSubject("Some Body")
	->setEmailCc("to@deltastateonline.com.au")
	->setEmailDate(date("Y-m-d H:i:s"))
	->setEmailFrom("Sample <from.email@deltastateonline.com.au>")
	->setFromEmailaddress("from.email@deltastateonline.com.au")
	->setEmailSubject("First Email")
	->setEmailTo("to@deltastateonline.com.au")	
	->setGuid("0000-0000-0000-0000-0000-".time())
	->setEmailBody("Add some body here")
	->setReplyTo("no value");
 
$repository->save($newEmail);


		