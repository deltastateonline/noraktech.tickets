PRAGMA foreign_keys=OFF;
BEGIN TRANSACTION;
CREATE TABLE recieved_emails(
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	guid VARCHAR(40) UNIQUE,
	emailfrom VARCHAR(255),
	emailto VARCHAR(255),
	emailcc VARCHAR(255),
	replyto VARCHAR(255),
	emailsubject VARCHAR(255),
	fromemailaddress VARCHAR(255),
	emailbody VARCHAR(512),
	attachments VARCHAR(512),
	responsesent BOOLEAN(1) default 0,
	created DATETIME,
	emaildate DATETIME
);
COMMIT;