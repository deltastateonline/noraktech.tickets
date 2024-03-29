CREATE TABLE `recieved_emails` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`guid` VARCHAR(40) NULL DEFAULT NULL,
	`emailfrom` VARCHAR(255) NULL DEFAULT NULL,
	`emailto` VARCHAR(255) NULL DEFAULT NULL,
	`emailcc` VARCHAR(255) NULL DEFAULT NULL,
	`replyto` VARCHAR(255) NULL DEFAULT NULL,
	`emailsubject` VARCHAR(255) NULL DEFAULT NULL,
	`fromemailaddress` VARCHAR(255) NULL DEFAULT NULL,
	`emailbody` VARCHAR(512) NULL DEFAULT NULL,
	`attachments` VARCHAR(512) NULL DEFAULT NULL,
	`responsesent` INT(1) NULL DEFAULT '0',
	`created` DATETIME NULL DEFAULT NULL,
	`emaildate` DATETIME NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `guid` (`guid`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;

ALTER TABLE `recieved_emails`
	CHANGE COLUMN `guid` `entityguid` VARCHAR(50) NULL DEFAULT NULL AFTER `id`;

ALTER TABLE `recieved_emails`
	ADD COLUMN `status` VARCHAR(10) NULL DEFAULT 'NEW' AFTER `emaildate`;
	
ALTER TABLE `recieved_emails`
	CHANGE COLUMN `entityguid` `entity_guid` VARCHAR(50) NULL DEFAULT NULL AFTER `id`;
