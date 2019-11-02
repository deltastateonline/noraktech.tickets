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