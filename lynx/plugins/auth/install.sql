CREATE TABLE users (
	id int(4) NOT NULL auto_increment,
	user varchar(65) NOT NULL default '',
	pass varchar(65) NOT NULL default '',
	PRIMARY KEY (`id`)
) TYPE=MyISAM ;
