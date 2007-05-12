DROP TABLE IF EXISTS products;
CREATE TABLE products (
  id_product int(11) unsigned NOT NULL auto_increment,
  id_category int(11) unsigned NOT NULL default '0',
  code varchar(20) NOT NULL default '',
  short_desc varchar(60) NOT NULL default '',
  long_desc text NOT NULL,
  date_added date NOT NULL default '0000-00-00',
  price decimal(10,2) NOT NULL default '0.00',
  amount mediumint(8) unsigned NOT NULL default '0',
  active tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (id_product),
  UNIQUE KEY code (code),
  KEY id_category (id_category),
  KEY date_added (date_added)
) TYPE=MyISAM;
INSERT INTO products (id_category, code, short_desc, long_desc, date_added, price, amount, active) VALUES ("1", "BK001", "War and Peace", "War and Peace, by Leo Tolstoy", "2005-06-23", "10.90", "15", "1");
INSERT INTO products (id_category, code, short_desc, long_desc, date_added, price, amount, active) VALUES ("1", "BK002", "1984", "1984, by George Orwell", "2005-06-24", "8.99", "10", "1");
INSERT INTO products (id_category, code, short_desc, long_desc, date_added, price, amount, active) VALUES ("1", "BK003", "The Complete Stories", "The Complete Stories, by Franz Kafka", "2005-06-25", "11.50", "12", "1");
INSERT INTO products (id_category, code, short_desc, long_desc, date_added, price, amount, active) VALUES ("1", "BK004", "The Old Man and the Sea", "The Old Man and the Sea, by Ernest Hemingway", "2005-06-25", "10.25", "15", "1");
INSERT INTO products (id_category, code, short_desc, long_desc, date_added, price, amount, active) VALUES ("1", "BK005", "One Hundred years of Solitude", "One Hundred years of Solitude, by Gabriel García Marquez", "2005-06-23", "14.30", "7", "1");
INSERT INTO products (id_category, code, short_desc, long_desc, date_added, price, amount, active) VALUES ("1", "BK006", "Don Quixote", "Don Quixote, by Miguel de Cervantes Saavedra", "2005-06-24", "11.75", "20", "1");
INSERT INTO products (id_category, code, short_desc, long_desc, date_added, price, amount, active) VALUES ("1", "BK007", "Gulliver\'s Travels", "Gulliver\'s Travels, by Jonathan Swift", "2005-06-25", "9.50", "12", "1");
INSERT INTO products (id_category, code, short_desc, long_desc, date_added, price, amount, active) VALUES ("1", "BK008", "Remembrance of Things Past", "Remembrance of Things Past, by Marcel Proust", "2005-06-25", "10.99", "17", "1");
INSERT INTO products (id_category, code, short_desc, long_desc, date_added, price, amount, active) VALUES ("1", "BK009", "The Complete Tales", "The Complete Tales, by Edgar Allan Poe", "2005-06-23", "9.99", "20", "1");
INSERT INTO products (id_category, code, short_desc, long_desc, date_added, price, amount, active) VALUES ("1", "BK010", "Moby Dick", "Moby Dick, by Herman Melville", "2005-06-24", "11.50", "15", "1");
INSERT INTO products (id_category, code, short_desc, long_desc, date_added, price, amount, active) VALUES ("2", "DV001", "The Godfather", "The Godfather (1972), directed by Fracis Ford Coppola, written by Mario Puzo", "2005-06-25", "8.00", "40", "1");
INSERT INTO products (id_category, code, short_desc, long_desc, date_added, price, amount, active) VALUES ("2", "DV002", "The Shawshank Redemption", "The Shawshank Redemption (1994), directed by Frank Darabont, written by Stephen King", "2005-06-25", "8.00", "40", "1");
INSERT INTO products (id_category, code, short_desc, long_desc, date_added, price, amount, active) VALUES ("2", "DV003", "The Lord of the Rings: The Return of the King", "The Lord of the Rings: The Return of the King (2003), directed by Peter Jackson, written by J.R.R. Tolkien", "2005-06-24", "9.00", "30", "1");
INSERT INTO products (id_category, code, short_desc, long_desc, date_added, price, amount, active) VALUES ("2", "DV004", "The Godfather: Part II", "The Godfather: Part II (1974), directed by Francis Ford Coppola, written by Mario Puzo", "2005-06-24", "8.00", "39", "1");
INSERT INTO products (id_category, code, short_desc, long_desc, date_added, price, amount, active) VALUES ("2", "DV005", "Shichinin no samurai", "Shichinin no samurai (1954), directed by Akira Kurosawa, written by Akira Kurosawa", "2005-06-24", "8.50", "30", "1");
INSERT INTO products (id_category, code, short_desc, long_desc, date_added, price, amount, active) VALUES ("2", "DV006", "Schindler\'s List", "Schindler\'s List (1993), directed by Steven Spielberg, written by Thomas Keneally", "2005-06-25", "9.90", "35", "1");
INSERT INTO products (id_category, code, short_desc, long_desc, date_added, price, amount, active) VALUES ("2", "DV007", "Casablanca", "Casablanca (1942), directed by Michael Curtiz, written by Murray Burnett and Joan Alison", "2005-06-25", "7.40", "40", "1");
INSERT INTO products (id_category, code, short_desc, long_desc, date_added, price, amount, active) VALUES ("2", "DV008", "Star Wars - A New Hope", "Star Wars - A New Hope (1977), directed by George Lucas, written by George Lucas", "2005-06-25", "9.00", "40", "1");
INSERT INTO products (id_category, code, short_desc, long_desc, date_added, price, amount, active) VALUES ("2", "DV009", "The Lord of the Rings: The Fellowship of the Ring", "The Lord of the Rings: The Fellowship of the Ring (2001), directed by Peter Jackson, written by J.R.R. Tolkien", "2005-06-25", "9.00", "32", "1");
INSERT INTO products (id_category, code, short_desc, long_desc, date_added, price, amount, active) VALUES ("2", "DV010", "Star Wars - The Empire Strikes Back", "Star Wars - The Empire Strikes Back (1980), directed by George Lucas, written by George Lucas", "2005-06-25", "9.25", "24", "1");