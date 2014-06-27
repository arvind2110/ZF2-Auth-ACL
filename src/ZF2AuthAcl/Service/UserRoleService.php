<?php

namespace ZF2AuthAcl\Service;

use Zend\Db\Sql\Ddl\Column\Integer;

class UserRoleService
{

    /**
     * @uses store all user role
     * 
     * @var Array
     */
    protected $userRole;
    
    /**
     * @uses store default user role
     * 
     * @var Integer
     */
    protected $defaultRole;
    
    /**
     * @uses store current user role
     * 
     * @var Integer
     */
    protected $currentRole;
    
    /**
     * @store Name of the role
     * 
     * @var String
     */
    protected $roleName;   
    
    /**
     * @store All valid role
     * 
     * @var Array
     */
    protected $validRole;
        

	public function mapper($data)
    {
        try{
            $this->currentRole = $data['currentRole'];
            $this->defaultRole = $data['defaultRole'];
            $this->userRole = $data['userRole'];
            $this->roleName = $data['roleName'];
            $this->validRole = $data['validRole'];
        }catch (\Exception $e){
            
            throw new \Exception($e->getPrevious()->getMessage());
        }
    }    

    
    /**
     * @return the $validRole
     */
    public function getValidRole()
    {
        return $this->validRole;
    }
    
    /**
     * @param multitype: $validRole
     */
    public function setValidRole($validRole)
    {
        $this->validRole = $validRole;
    }
    
	/**
     * @return the $userRole
     */
    public function getUserRole()
    {
        return $this->userRole;
    }

	/**
     * @return the $defaultRole
     */
    public function getDefaultRole()
    {
        return $this->defaultRole;
    }

	/**
     * @return the $currentRole
     */
    public function getCurrentRole()
    {
        return $this->currentRole;
    }

	/**
     * @return the $roleName
     */
    public function getRoleName()
    {       
        return $this->roleName;
    }

	/**
     * @param multitype: $userRole
     */
    public function setUserRole($userRole)
    {
        $this->userRole = $userRole;
    }

	/**
     * @param \Zend\Db\Sql\Ddl\Column\Integer $defaultRole
     */
    public function setDefaultRole($defaultRole)
    {
        $this->defaultRole = $defaultRole;
    }

	/**
     * @param \Zend\Db\Sql\Ddl\Column\Integer $currentRole
     */
    public function setCurrentRole($currentRole)
    {
        $this->currentRole = $currentRole;
    }

	/**
     * @param string $roleName
     */
    public function setRoleName($roleName)
    {
        $this->roleName = $roleName;
    }

 
}
