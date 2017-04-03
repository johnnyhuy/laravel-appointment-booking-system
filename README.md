# SE-PT-Assignment
Major group assignment for COSC2299 Software Engineering: Process and Tools at RMIT University. This assignment is due 9:00AM Monday April 10 2017.

## Team Members & Contributions
For team members, please add more details to your role if needed.

* **Johnny Huynh** S3604367 - **25%**
  * Manage Trello board
  * Setup/manage live server
* **Lachlan Porter** S3537901 - **25%**
  * UI design
  * User stories
* **Craig Barry** S3601725 - **25%**
  * Unit testing
  * Database planning
* **Jarry Chen** S3600396 - **25%**
  * User stories
  * Unit testing

# Development Environment
To allow actual development on the project code. You must require a development environment for Laravel (PHP Framework) to function properly on your local device.

## Requirments

* [Composer](https://getcomposer.org/download/) >= 1.4.1
* [PHP](http://php.net/manual/en/intro-whatis.php) >= 5.6.4
* [Git Bash](https://git-for-windows.github.io/) or any other CMD with git
* Text Editor ([Sublime Text 3](https://www.sublimetext.com/3), Notepad++)
* [SQLite Browser](http://sqlitebrowser.org/) or any other SQLite viewer

## Install/Configuration/Running a Dev Server

### PHP

Download PHP (VC14 x64 Thread Safe) for [Windows](http://windows.php.net/download#php-7.1) and follow this [install guide](https://www.sitepoint.com/how-to-install-php-on-windows/).

For other OS's, just do a simple Google search to install PHP.

PHP requires the following extensions enabled in your **php.ini** at your PHP installed directory.
* OpenSSL PHP Extension
* PDO PHP Extension
* Mbstring PHP Extension
* Tokenizer PHP Extension
* XML PHP Extension

### Cofigure .env for Laravel

This will make sure the database is using **SQLite** on your machine by linking the database directory in your **.env** file (side-note: MySQL is used on the live server).

* Go to root git repo of the project (SE-PT-Assignment)
* Find **'.env.example'**
* Rename **'.env.example'** to **'.env'**
* Change **'DB_DATABASE='** to **'DB_DATABASE={ROOT}/database/dev-databse.sqlite'**
  * Where **{ROOT}** is the repo directory
  * e.g. 'C:/Users/Johnny/SE-PT-Assignment/database/dev-database.sqlite'
* Save .env

### Install Composer Dependancies

This is needed to install all of Laravels PHP dependancies through Composer.

* Open Git Bash or any CMD
* CD to the repo directory (SE-PT-Assignment)
* Run the following command

```
composer install
```

### Add Application Key

This is needed to avoid an authenication error on Laravel when you setup local development on your machine.

* Open Git Bash or any CMD
* CD to the repo directory (SE-PT-Assignment)
* Run the following command

```
php artisan key:generate
```

What is command would do is it will generate an application key and paste it into the .env file.

### Running a Dev Server

* CD to the repo directory (SE-PT-Assignment)
* Start a dev server by running the following command

```
php artisan serve
```

* Open a browser
* Visit [localhost:8000](localhost:8000)
  * Laravel should by default create a server at port 8000

### Using the 'dev' branch on Git

* Open Git Bash or any CMD
* CD to the repo directory (SE-PT-Assignment)
* Checkout to the 'dev' branch with the following command
```
git checkout dev
```
* Run another command to keep the branch up to date
```
git pull
```

## Troubleshooting

### RuntimeException in Encrypter.php line 43: The only supported ciphers are AES-128-CBC and AES-256-CBC with the correct key lengths.

Make sure you generated an applciation key for Laravel. APP_KEY in .env file is either missing or incorrect.

```
php artisan key:generate
```

### [PDOException] SQLSTATE[HY000] [14] unable to open database file

The link to the database file is missing. In this case the database file is in the repo at **"database/dev-database.sqlite"**.

Haven't enabled PHP extension **'extension=php_pdo_mysql.dll'** in the **'php.ini'** file.

