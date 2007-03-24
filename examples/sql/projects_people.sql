USE test;

DROP TABLE IF EXISTS projects_people;

CREATE TABLE projects_people (
  id_project int(11) NOT NULL default '0',
  id_people int(11) NOT NULL default '0',
  PRIMARY KEY  (id_project,id_people)
);