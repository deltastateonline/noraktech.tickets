## How to run this features

Make sure that you have sqlite installed.

Make sure that you have "ext\php_mailparser.dll"


```bash
F:\webdocs\noraktech.tickets>c:\sqlite\sqlite3.exe recievedemails.db
```
Copy and paste the content of `recieved.sqlite.ddl` into the console.
Install the following extension for chrome Sqlite Manager 0.2.7

### Or 

Create a mysql table using content of `recieved.mysql.ddl` 

Update `config.inc` file to `config.php` with configuration details.


### Install composer dependency
```bash
F:\webdocs\noraktech.tickets>composer install
```


### Run this test script
```bash
F:\webdocs\noraktech.tickets>php emailbox.processor\test.orm.php
```


### Run this command like this.
Do not change into the `emailbox.processor` folder
```bash
F:\webdocs\noraktech.tickets>php emailbox.processor\process.mailbox.php
```
### Help For Micro Orm
[Readme for Micro Orm](https://opensource.byjg.com/micro-orm)
