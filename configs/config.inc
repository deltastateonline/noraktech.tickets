<?php
$config["ports"] = array(
	"ssl" => ":993",
	"nossl" => ":143/novalidate-cert"
);

$config["server-url"] = "mail.webaddress.com";

$config["login-details"] = array(
		"username"=>"anbox@yourwebsite.com",
		"password"=> "somepassword"
);

$config["database-connection"]["sqlite"] = "sqlite:db/recievedemails.db";
$config["database-connection"]["mysql"] = "mysql://username:password@localhost/database";


$config["smtp"] = array(
		"hostname"=>"smtp server",
		"username"=>"username",
		"password"=>"password",
		"send-emails-to" =>"set this value during testing",
		"sent-from"=>"all remails will come from this email address"
);