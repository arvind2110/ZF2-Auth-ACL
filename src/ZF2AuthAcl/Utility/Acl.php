<?php
namespace ZF2AuthAcl\Utility;

use Zend\Permissions\Acl\Acl as ZendAcl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Acl extends ZendAcl implements ServiceLocatorAwareInterface
{

    const DEFAULT_ROLE = 'guest';

    protected $_roleTableObject;

    protected $serviceLocator;

    protected $roles;

    protected $permissions;

    protected $resources;

    protected $rolePermission;

    protected $commonPermission;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        
        return $this;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function initAcl()
    {
        $this->roles = $this->_getAllRoles();
        $this->resources = $this->_getAllResources();
        $this->rolePermission = $this->_getRolePermissions();
        // we are not putting these resource & permission in table bcz it is
        // common to all user  
           
        $this->commonPermission = array(
            'zfcuser' => array(
                'logout',
                'login',
                'register'                
            ),
            'goalioforgotpassword_forgot' => array(
        	   'forgot',
                'reset',
            ),
        );
        
        $this->_addRoles()
            ->_addResources()
            ->_addRoleResources();
    }

    public function isAccessAllowed($role, $resource, $permission)
    {
        if (! $this->hasResource($resource)) {
            return false;
        }
        if ($this->isAllowed($role, $resource, $permission)) {
            return true;
        }
        return false;
    }

    protected function _addRoles()
    {
        $this->addRole(new Role(self::DEFAULT_ROLE));
        
        if (! empty($this->roles)) {
            foreach ($this->roles as $role) {
                $roleName = $role['role_name'];
                if (! $this->hasRole($roleName)) {
                    $this->addRole(new Role($roleName), self::DEFAULT_ROLE);
                }
            }
        }
        return $this;
    }

    protected function _addResources()
    {
        if (! empty($this->resources)) {
            foreach ($this->resources as $resource) {
                if (! $this->hasResource($resource['resource_name'])) {
                    $this->addResource(new Resource($resource['resource_name']));
                }
            }
        }
        
        // add common resources
        if (! empty($this->commonPermission)) {
            foreach ($this->commonPermission as $resource => $permissions) {
                if (! $this->hasResource($resource)) {
                    $this->addResource(new Resource($resource));
                }
            }
        }
        
        return $this;
    }

    protected function _addRoleResources()
    {
        // allow common resource/permission to guest user
        if (! empty($this->commonPermission)) {
            foreach ($this->commonPermission as $resource => $permissions) {
                foreach ($permissions as $permission) {
                    $this->allow(self::DEFAULT_ROLE, $resource, $permission);
                }
            }
        }
        
        if (! empty($this->rolePermission)) {
            foreach ($this->rolePermission as $rolePermissions) {
                $this->allow($rolePermissions['role_name'], $rolePermissions['resource_name'], $rolePermissions['permission_name']);
            }
        }
        
        return $this;
    }

    /**
     * get All roles
     * 
     * @return unknown
     */
    protected function _getAllRoles()
    {
        $cache = $this->getServiceLocator()->get('Zend\Cache\Storage\Filesystem');
        $allRoles = $cache->getItem('allRoles');
        if(isset($allRoles)){
            return $allRoles;
        }else {
            $roleTable = $this->getServiceLocator()->get("RoleTable");
            $allRoles = $roleTable->getUserRoles();
            $cache->setItem('allRoles',$allRoles);
            return $allRoles;
        }
    }

    /**
     * get All resources
     * @return unknown
     */
    protected function _getAllResources()
    {
        $cache = $this->getServiceLocator()->get('Zend\Cache\Storage\Filesystem');
        $allResource= $cache->getItem('allResource');
        if(isset($allResource)){
            return $allResource;
        }else {
            $resourceTable = $this->getServiceLocator()->get("ResourceTable");
            $allResource= $resourceTable->getAllResources();
            $cache->setItem('allResource',$allResource);
            return $allResource;
        }
    }

    /**
     * get all permissions
     * 
     * @return unknown
     */
    protected function _getRolePermissions()
    {
        $cache = $this->getServiceLocator()->get('Zend\Cache\Storage\Filesystem');
        $RolePermissions = $cache->getItem('RolePermissions');
        if(isset($RolePermissions)){
            return $RolePermissions;
        }else{
            $rolePermissionTable = $this->getServiceLocator()->get("RolePermissionTable");
            $RolePermissions =  $rolePermissionTable->getRolePermissions();
            $cache->setItem('RolePermissions',$RolePermissions);
            return $RolePermissions;
        }
        
    }
    
    private function debugAcl($role, $resource, $permission)
    {
        echo 'Role:-' . $role . '==>' . $resource . '\\' . $permission . '<br/>';
    }
}
