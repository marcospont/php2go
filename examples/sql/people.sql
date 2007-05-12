DROP TABLE IF EXISTS people;
CREATE TABLE people (
  id_people int unsigned not null auto_increment,
  name varchar(50) not null,
  sex enum('M', 'F') not null,
  birth_date date not null,
  address varchar(100) not null,
  id_country int unsigned not null,
  notes text null,
  add_date datetime not null,
  active smallint(1) unsigned not null default 0,
  primary key (id_people)
);