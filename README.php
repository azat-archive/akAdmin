<?

/*
 * This file is part of the akAdmin package.
 * (c) 2010 Azat Khuzhin <dohardgopro@gmail.com>
 *
 * For the full copyright and license information, please view http://www.gnu.org/licenses/gpl-2.0.html
 */

/**

@author Azat Khuzhin <dohardgopro@gmail.com>
@package akLib
@license GPLv2

akAdmin - Azat Khuzhin Admin System
Begin date: 20.07.10
Requires: >= PHP 5.2
Please use set_include_path() function
And don`t forget about extract() function
And using Exceptions

Small descriptions:

This admin system are layer between user and database

For working with javascript, this project using:
1. jquery (@link http://jquery.com/)
2. jquery autocomplete (@link http://docs.jquery.com/Plugins/Autocomplete)
3. jquery alerts (@link http://abeautifulsite.net/blog/2008/12/jquery-alert-dialogs/)
4. sprintf (@link http://perldoc.perl.org/functions/sprintf.html)

For grants model using power of "2"
Using akLib (my lib, after it I begin write this admin system)

If no users in users table, than one will be created, with the following data:
Default user: admin
Default password: secretPassword

START and COFIGURATIONS
1. Configurations
	@see /includes/config.php

2. Start
	2.1. Configure your web server
	2.2.
		Replace table names in file /dumps/akAdmin_StandartTables.sql 
		In this dump file project name is "test", so you must replace "`test*`" to "project*`",
		where "project" - is a const defined in /includes/config.php
	2.3. Create new DB in your DB server (default: akAdmin, your can configure it by const "dbName" in /includes/config.php)
	2.4.
		Import dump /dumps/akAdmin_StandartTables.sql and can work!
		Notice: to import data in *nix based system your can run command
		"cat /dumps/akAdmin_StandartTables.sql | mysql -uYOUR_USER -pYOUR_PASSWORD dbName"
*/