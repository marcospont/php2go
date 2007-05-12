CREATE TABLE `users` (
  `user_id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `login` varchar(20) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `login` (`login`)
) TYPE=MyISAM;
INSERT INTO `users` (`name`, `login`, `password`) VALUES ('System Administrator', 'admin', md5('admin'));