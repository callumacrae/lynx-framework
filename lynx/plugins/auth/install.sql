CREATE TABLE users (
	id int(4) NOT NULL auto_increment,
	user varchar(65) NOT NULL default '',
	pass varchar(65) NOT NULL default '',
	cookie char(32) binary NOT NULL default '',
	session char(32) binary NOT NULL default '',
	ip varchar(15) binary NOT NULL default '',
	PRIMARY KEY (`id`)
) TYPE=MyISAM ;
