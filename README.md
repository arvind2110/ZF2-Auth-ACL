ZF2-Auth-ACL
============

Branch: zfcuser_acl
-------------------

This this the ZF2 ACL module forked from arvind2110/ZF2-Auth-ACL and Plugged with zfcuser module. it will provide role base access and switching between roles , provides custom permission denied template , plug ins to access role at controller , view and module level. All the role, resource and permission are stored in databases.

How to Use it
-------------------

using composer add 
```
  "require" : {
       ...
    "mohit-singh/zf2auth-acl": "V1.0.0"
  }
```
then copy and rename the following,
```
copy vendor/mohit-singh/zf2auth-acl/config/aclAuth.local.php.dist to config/autoload/aclAuth.local.php
```
Add the depended table from 
```
vendor/mohit-singh/zf2auth-acl/data/data.sql
```

ADD role for user in table  e.g.
```
INSERT INTO `role` (`role_name`, `status`) VALUES ('Role1', 'Active');
INSERT INTO `role` (`role_name`, `status`) VALUES ('Role2', 'Active');
INSERT INTO `role` (`role_name`, `status`) VALUES ('Role3', 'Active');
```

ADD resources, resources are your controller name through which you invoke your controller, for me it's "Application\Controller\Index" e.g.
```
INSERT INTO `resource` (`resource_name`) VALUES ('Application\\Controller\\Index');
```
ADD Permissions , permission are the action, you have to associated all action with there controller resource  e.g.
```
INSERT INTO `permission` (`permission_name`, `resource_id`) VALUES ('index', 1);
INSERT INTO `permission` (`permission_name`, `resource_id`) VALUES ('show', 1);
```
ADD role permission , you have to decided which role have which permission
e.g.

```
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 1);
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 2);
```

ADD user role , you have to decide which user have which role , this can be done manually or using some custom script.
```
INSERT INTO `user_role` (`user_id`, `role_id`) VALUES (1, 1);
INSERT INTO `user_role` (`user_id`, `role_id`) VALUES (2, 2);
```

NOTE:- please check the aclAuth.local.php con-fig for the default role, it Should be one of the role  whatever you insert in the database.

after all these configuration is done you are ready to use ACL module

Services
------------

Remove ACL from a URL and make it global, access to all ,
add link here
```php
// in config/autoload/aclAuth.local.php
'globalList' => array(
		      'Application\Controller\Index-index'
	   ),
```

Remove ACL from a URL and make it global before login ,
add link here
```php
// in config/autoload/aclAuth.local.php
'beforeLoginList' => array(
		      'Application\Controller\Index-index'
	   ),
```
Custom template for permission denied, add new template path here
```php
// in config/autoload/aclAuth.local.php
'ACL_Template' =>'zf2-auth-acl/index/permission.phtml'
```
Role base services at controller
```php
// Check user has role or not , return true, false
$this->userAuthRole()->userHasRole();

//Get  user current role
$this->userAuthRole()->getRoleName();

//get All valid role for the current user
$this->userAuthRole()->getUserValidRole();

//Switch between roles
$this->userAuthRole()->switchRole('ADMIN');
```
at view level

```php
// Check user has role or not , return true, false
$this->roleAuth()->userHasRole();

//Get  user current role
$this->roleAuth()->getRoleName();

//get All valid role for the current user
$this->roleAuth()->getUserValidRole();

//Switch between roles
$this->roleAuth()->switchRole('ADMIN');
```
at module level

```php
$roleAtuth = $serviceManager->get('roleAuthService');

// Check user has role or not , return true, false
$roleAtuth->userHasRole();

//Get  user current role
$roleAtuth->getRoleName();

//get All valid role for the current user
$roleAtuth->getUserValidRole();

//Switch between roles
$roleAtuth->switchRole('ADMIN');
```


