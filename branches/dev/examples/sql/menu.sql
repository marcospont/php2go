DROP TABLE IF EXISTS `menu`;
CREATE TABLE IF NOT EXISTS `menu` (
  `id_menu` int(11) unsigned NOT NULL auto_increment,
  `id_parent_menu` int(11) unsigned default NULL,
  `caption` varchar(50) NOT NULL default '',
  `link` varchar(100) default NULL,
  PRIMARY KEY  (`id_menu`)
) TYPE=MyISAM COMMENT='Menu';
TRUNCATE TABLE `menu`;
INSERT INTO `menu` (id_parent_menu, caption, link) VALUES (null, 'Products', 'products.php');
INSERT INTO `menu` (id_parent_menu, caption, link) VALUES (1, 'Books »', null);
INSERT INTO `menu` (id_parent_menu, caption, link) VALUES (2, 'Fiction', 'products.php?category=1');
INSERT INTO `menu` (id_parent_menu, caption, link) VALUES (2, 'Non-Fiction', 'products.php?category=2');
INSERT INTO `menu` (id_parent_menu, caption, link) VALUES (1, 'Toys', 'products.php?category=3');
INSERT INTO `menu` (id_parent_menu, caption, link) VALUES (1, 'Audio & Video', 'products.php?category=4');
INSERT INTO `menu` (id_parent_menu, caption, link) VALUES (null, 'Online Store', null);
INSERT INTO `menu` (id_parent_menu, caption, link) VALUES (7, 'About us', 'about.php');
INSERT INTO `menu` (id_parent_menu, caption, link) VALUES (7, 'Order information', 'order_info.php');
INSERT INTO `menu` (id_parent_menu, caption, link) VALUES (7, 'Online support', 'online_support.php');
INSERT INTO `menu` (id_parent_menu, caption, link) VALUES (null, 'My Cart', 'cart.php');
INSERT INTO `menu` (id_parent_menu, caption, link) VALUES (null, 'Logout', 'logout.php');