# VintageGames
An old-style gaming blog made for the web-programming course at computer science school.
It works with XAMPP platform, insert the project inside
/htdocs folder and run the apache and mysql server.To use database functionality (logging and score system) you must execute the following queries in /phpmyadmin:

CREATE DATABASE game_blog;

CREATE TABLE `game_blog`.`users` (
`username` VARCHAR(20) CHARACTER SET utf8 COLLATE
utf8_general_ci NOT NULL ,
`password` CHAR(32) CHARACTER SET utf8 COLLATE
utf8_general_ci NOT NULL ,
`email` VARCHAR(255) CHARACTER SET ucs2 COLLATE
ucs2_general_ci NOT NULL ,
`record_snake` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
`record_pong` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
`auth_code` VARCHAR(8) CHARACTER SET utf8 COLLATE
utf8_general_ci NOT NULL ,
`flag_authenticated` TINYINT(1) NOT NULL DEFAULT '0' ,
`time_session` INT(12) NOT NULL DEFAULT '0' ,
`session_id` VARCHAR(20) CHARACTER SET utf8 COLLATE
utf8_general_ci NULL DEFAULT NULL ,
PRIMARY KEY (`username`(20))) ENGINE = InnoDB;

The project use the email sending library PHPMailer:  https://github.com/PHPMailer/PHPMailer. You must download it and 
insert into the project folder in order to work. 

