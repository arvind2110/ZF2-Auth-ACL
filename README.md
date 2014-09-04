ZF2-Auth-ACL
============

ZF2-Auth-ACL
------------
Simple module to implement Zenb Auth and Zend ACL in Zend Framework 2


Database tables required
------------------------

Following database tables are required to use this module. You can modify the tables and table information as per your need. Also make changes in code regarding the same.

```mysql

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `status` enum('Y','N') NOT NULL DEFAULT 'Y',
  `created_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8

CREATE TABLE `role` (
  `rid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(45) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  PRIMARY KEY (`rid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8

CREATE TABLE `user_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8

CREATE TABLE `resource` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `resource_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

CREATE TABLE `permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(45) NOT NULL,
  `resource_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1

CREATE TABLE `role_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


/* Add Roles */

INSERT INTO `demo`.`role` (`role_name`, `status`) VALUES ('Role1', 'Active');
INSERT INTO `demo`.`role` (`role_name`, `status`) VALUES ('Role2', 'Active');
INSERT INTO `demo`.`role` (`role_name`, `status`) VALUES ('Role3', 'Active');

/* Add Rresorces */

INSERT INTO `demo`.`resource` (`resource_name`) VALUES ('Application\\Controller\\Index');
INSERT INTO `demo`.`resource` (`resource_name`) VALUES ('ZF2AuthAcl\\Controller\\Index');

/* Add Users */
INSERT INTO `demo`.`users` (`email`, `password`, `status`) VALUES ('example.1@example.com', 'd7d833534a39afbac08ec536bed7ae9eeac45638', 'Y');
INSERT INTO `demo`.`users` (`email`, `password`, `status`) VALUES ('example.2@example.com', 'd7d833534a39afbac08ec536bed7ae9eeac45638', 'Y');
INSERT INTO `demo`.`users` (`email`, `password`, `status`) VALUES ('example.3@example.com', 'd7d833534a39afbac08ec536bed7ae9eeac45638', 'Y');

/* Add User Roles */
INSERT INTO `demo`.`user_role` (`user_id`, `role_id`) VALUES (1, 1);
INSERT INTO `demo`.`user_role` (`user_id`, `role_id`) VALUES (2, 2);
INSERT INTO `demo`.`user_role` (`user_id`, `role_id`) VALUES (3, 3);

/* Add Permissions */
INSERT INTO `demo`.`permission` (`permission_name`, `resource_id`) VALUES ('index', 1);
INSERT INTO `demo`.`permission` (`permission_name`, `resource_id`) VALUES ('index', 2);
INSERT INTO `demo`.`permission` (`permission_name`, `resource_id`) VALUES ('show', 1);
INSERT INTO `demo`.`permission` (`permission_name`, `resource_id`) VALUES ('test', 1);

/* Add User Role Permissions */
INSERT INTO `demo`.`role_permission` (`role_id`, `permission_id`) VALUES (1, 1);
INSERT INTO `demo`.`role_permission` (`role_id`, `permission_id`) VALUES (1, 2);
INSERT INTO `demo`.`role_permission` (`role_id`, `permission_id`) VALUES (1, 3);
INSERT INTO `demo`.`role_permission` (`role_id`, `permission_id`) VALUES (1, 4);
INSERT INTO `demo`.`role_permission` (`role_id`, `permission_id`) VALUES (2, 1);
INSERT INTO `demo`.`role_permission` (`role_id`, `permission_id`) VALUES (2, 2);
INSERT INTO `demo`.`role_permission` (`role_id`, `permission_id`) VALUES (3, 1);
INSERT INTO `demo`.`role_permission` (`role_id`, `permission_id`) VALUES (3, 3);

```

White List
----------

There are some pages which does not require authentication(Auth) or authrization(Acl). So, we include settings for those pages in terms of cotroller name, action name, module name.
