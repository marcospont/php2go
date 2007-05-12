DROP TABLE IF EXISTS menu;
CREATE TABLE menu (
  id_menu int(11) unsigned NOT NULL auto_increment,
  id_parent_menu int(11) unsigned default NULL,
  caption varchar(50) NOT NULL default '',
  link varchar(100) default NULL,
  PRIMARY KEY  (id_menu)
) AUTO_INCREMENT=13 COMMENT='Menu';
insert into menu (id_menu, id_parent_menu, caption, link) values (1, NULL, 'Products', '');
insert into menu (id_menu, id_parent_menu, caption, link) values (2, 1, 'Books »', NULL);
insert into menu (id_menu, id_parent_menu, caption, link) values (3, 2, 'Fiction', '?op=Books/Fiction');
insert into menu (id_menu, id_parent_menu, caption, link) values (4, 2, 'Non-Fiction', '?op=Books/Non-Fiction');
insert into menu (id_menu, id_parent_menu, caption, link) values (5, 1, 'Toys', '?op=Toys');
insert into menu (id_menu, id_parent_menu, caption, link) values (6, 1, 'Audio & Video', '?op=Audio/Video');
insert into menu (id_menu, id_parent_menu, caption, link) values (7, NULL, 'Online Store »', NULL);
insert into menu (id_menu, id_parent_menu, caption, link) values (8, 7, 'About us', '?op=About');
insert into menu (id_menu, id_parent_menu, caption, link) values (9, 7, 'Order information', '?op=Order Info');
insert into menu (id_menu, id_parent_menu, caption, link) values (10, 7, 'Online support', '?op=Support');
insert into menu (id_menu, id_parent_menu, caption, link) values (11, NULL, 'My Cart', '?op=Cart');
insert into menu (id_menu, id_parent_menu, caption, link) values (12, NULL, 'Logout', '?op=Logout');