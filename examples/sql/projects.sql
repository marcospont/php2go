USE test;

DROP TABLE IF EXISTS projects;

CREATE TABLE projects (
  id_project int(11) unsigned NOT NULL auto_increment,
  id_manager int(11) NOT NULL default '0',
  name varchar(40) NOT NULL default '',
  start_date date NOT NULL default '0000-00-00',
  end_date date NOT NULL default '0000-00-00',
  PRIMARY KEY  (id_project)
);