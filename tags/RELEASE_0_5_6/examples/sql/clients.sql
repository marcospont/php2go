DROP TABLE IF EXISTS client;
CREATE TABLE client (
  client_id int(11) unsigned NOT NULL auto_increment,
  name varchar(100) NOT NULL default '',
  address varchar(100) NOT NULL default '',
  category varchar(20) NOT NULL default '',
  active smallint(1) NOT NULL default '1',
  PRIMARY KEY  (client_id)
) COMMENT='Clients';
INSERT INTO client (name,address,category,active) VALUES ('Foo','5th Avenue, 125','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Bar','4th Avenue, 333','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Baz','Wall Street, 2344','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('John','Beer Street, 1201','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Mary','Sea Street, 25','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Adam','France Avenue, 1445','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Paul','Brazil Avenue, 3402','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Richard','Mexico Avenue, 1225 room 4','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Harry','France Street, 2049','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Bill','Microsoft Avenue, 3144','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Jennifer','Cuba Street, 21','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Daisy','Flower Avenue, 114','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Patrick','England Street, 814 room 201','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Claudia','Spain Avenue, 1223','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Anna','China Street, 3211','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Christina','Japan Avenue, 15','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('David','Church Street, 866','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Joe','Docks Street, 1429 room 3','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Erick','School Road','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Gary','Third Avenue','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Sarah','Italy Avenue','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Joseph','Fifth Street','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('James','Chester Avenue','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Phillip','Lake Road','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Francis','Old Country Road, 455','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Martin','Autumn Place, 2010','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Kim','Russia Street, 1877','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Sandra','Central Street, 631','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Nelson','P.O. Box 211','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Meredith','P.O. Box 290','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Susan','P.O. Box 390','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Todd','P.O. Box 34','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Randy','P.O. Box 101','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Judith','P.O. Box 94','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Laura','P.O. Box 345','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Kathleen','P.O. Box 65','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Michael','St. Paul Street','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Deborah','St. Mary Street','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Catherine','4th Avenue','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Scott','4th Avenue','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Pamela','5th Avenue','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Nick','5th Avenue','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Mike','P.O. Box 567','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Cheryl','P.O. Box 44','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Victoria','Lake Road','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Ronald','6th Avenue, 1277','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Bob','2nd Avenue, 443','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Margaret','1st Avenue, 699','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Glenda','P.O. Box 141','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Ken','Sloan Place, 363','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Barbara','Finland Street, 611','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Thomas','Industrial Drive, 992','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Kathy','School Road, 1089 room 14','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Patricia','Exchange Street, 2244','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Michelle','P.O. Box 55','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Karen','Park Avenue, 11','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Woody','Sea Avenue, 88','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Fred','Canada Avenue, 547','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Natalie','North Highway, 4555','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Patricia','South Highway, 1558','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Adrienne','3rd Avenue, 3177','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Brian','P.O. Box 14','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Anthony','Clouds Street','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Paula','President Avenue','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Thomas','Wall Street, 3444','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Angela','3rd Avenue, 1099','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Melissa','Brazil Avenue, 4455','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Stephen','Mexico Avenue, 3441','Common',1);
INSERT INTO client (name,address,category,active) VALUES ('Mike','United States Street, 765','Master',1);
INSERT INTO client (name,address,category,active) VALUES ('Bart','Park Avenue, 1000 room 10','Master',1);