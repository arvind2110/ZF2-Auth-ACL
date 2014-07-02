<?php
namespace ZF2AuthAcl\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use ZF2AuthAcl\Service\UserRoleService;
use Zend\Session\Container;

class userAuthRole extends AbstractPlugin
{
    
    protected $_roleService;
    
    /**
     * 
     */
    public function __construct()
    {
        $roleRepo = new Container('roleReop');
        $this->_roleService = $roleRepo->offsetGet('roleServiceObj');
    }
    
    /**
     * 
     * @return boolean
     */
    public function userHasRole()
    {
       if(isset($this->_roleService)){
           return true;
       }else {
           return false;
       }
    }
        
    /**
     * 
     * @param unknown $userId
     * @param unknown $sm
     * @throws \Exception
     * @return unknown
     */
    public function setUserRole($userId,$sm)
    {
        try{
            $roleTable = $sm->get('UserRoleTable');
            $roleDetails = $roleTable->getUserRole(array('user_id' => $userId,'status' => 'Active'));
            $config = $sm->get('config');
            
            if(isset($config['authRoleSettings']) && !empty($config['authRoleSettings'])){
                $defaultRole = $config['authRoleSettings']['defaultRaoleId'];
            }else {
                throw new \Exception('Role Auth settings are missing.');
            }            
            if(!empty($roleDetails)){
                $validRole = array();
                foreach ($roleDetails as $role){
                    $validRole[] = $role['role_name'];
                }                
                if(!in_array($defaultRole,$validRole)){
                    throw new \Exception(sprintf('%s is not define for this user',$defaultRole));
                }                
                $mapperData = array(
                	'currentRole' => $defaultRole,
                    'defaultRole'=> $defaultRole,
                    'roleName' => $defaultRole,
                    'userRole' => $roleDetails,
                    'validRole' => $validRole,
                );
                $userRoleService = new UserRoleService();
                $userRoleService->mapper($mapperData);
                $roleRepo = new Container('roleReop');                
                $roleRepo->offsetSet('roleServiceObj', serialize($userRoleService));                
                return $defaultRole;
                
            }else {
                throw new \Exception('User must have some role');
            }
        } catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }
    
    /**
     * 
     * @throws \Exception
     */
    public function getRoleName()
    {
        if(isset($this->_roleService)){
            $this->_roleService = unserialize( $this->_roleService);            
            return $this->_roleService->getRoleName();
            
        }else {
            throw new \Exception('User role is not set');
        }
    }
    
    /**
     * 
     * @param unknown $role
     * @throws \Exception
     */
    public function switchRole($role)
    {
        if(isset($this->_roleService)){
            $this->_roleService = unserialize($this->_roleService);
            $neRoleService = new UserRoleService();
            $validRole = $this->_roleService->getValidRole();           
            if(in_array($role, $validRole)){
                $neRoleService->setCurrentRole($role);
                $neRoleService->setDefaultRole($this->_roleService->getDefaultRole());
                $neRoleService->setUserRole($this->_roleService->getUserRole());
                $neRoleService->setValidRole($this->_roleService->getValidRole());
                $neRoleService->setRoleName($role);
                $roleRepo = new Container('roleReop');
                $roleRepo->offsetSet('roleServiceObj', serialize($neRoleService));
                $this->_roleService = $neRoleService;
            }
            $this->_roleService = serialize($this->_roleService);  
        }else {
            throw new \Exception('User role is not set');
        }
    }
    
    /**
     * 
     * @throws \Exception
     */
    public function getUserValidRole()
    {
        if(isset($this->_roleService)){
            $this->_roleService = unserialize( $this->_roleService);
            return $this->_roleService->getValidRole();
        
        }else {
            throw new \Exception('User role is not set');
        }
    }
}