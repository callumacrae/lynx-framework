CREATE TABLE users (
	id int(12) NOT NULL auto_increment,
	user varchar(65) NOT NULL default '',
	pass varchar(65) NOT NULL default '',
	cookie varchar(32) binary NOT NULL default '',
	session varchar(32) binary NOT NULL default '',
	ip varchar(15) binary NOT NULL default '',
	PRIMARY KEY (`id`)
) TYPE=MyISAM ;
