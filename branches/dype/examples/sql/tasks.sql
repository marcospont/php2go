DROP TABLE IF EXISTS tasks;
CREATE TABLE tasks (
  id_task int(11) unsigned NOT NULL auto_increment,
  id_project int(11) NOT NULL default '0',
  id_owner int(11) NOT NULL default '0',
  name varchar(20) NOT NULL default '',
  description varchar(200) NOT NULL default '',
  start_date date NOT NULL default '0000-00-00',
  end_date date NOT NULL default '0000-00-00',
  priority char(1) NOT NULL default '',
  status char(1) NOT NULL default '',
  PRIMARY KEY  (id_task)
);