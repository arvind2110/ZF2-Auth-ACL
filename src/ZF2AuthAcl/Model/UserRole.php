<?php
namespace ZF2AuthAcl\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Complysight\Service\UserAuthAdapter;
use Zend\Session\Container;
use Complysight\Service\UserPassword;
use Zend\Db\Sql\Update;
use Zend\Db\Sql\Delete;
use Zend\Validator\Explode;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;

class UserRole extends AbstractTableGateway
{

    public $table = 'user_role';

    public function __construct(Adapter $adapter)
    {
        $this->resultSetPrototype = new ResultSet(ResultSet::TYPE_ARRAY);
        $this->initialize();
    }
    
    public function getUserRoles($where = array(), $columns = array(), $orderBy = '', $paging = false)
    {
        try {
            $sql = new Sql($this->getAdapter());
            $select = $sql->select()->from(array(
                'sa' => $this->table
            ));
            
            if (count($where) > 0) {
                $select->where($where);
            }
            
            $select->where($where);
            
            if (count($columns) > 0) {
                $select->columns($columns);
            }
            
            if (! empty($orderBy)) {
                $select->order($orderBy);
            }
            
            if ($paging) {
                
                $dbAdapter = new DbSelect($select, $this->getAdapter());
                $paginator = new Paginator($dbAdapter);
                
                return $paginator;
            } else {
                $statement = $sql->prepareStatementForSqlObject($select);
                
                $clients = $this->resultSetPrototype->initialize($statement->execute())
                    ->toArray();
                
                return $clients;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getPrevious()->getMessage());
        }
    }

    /**
     * Function for Saving and Updating the Employee Records
     *
     * @param unknown $employeeData            
     * @throws \Exception
     * @return boolean
     */
    public function saveUserRoles($userRoleData)
    {
        // ////////Delete All the Previous Roles if Exits and make new entries//
        $this->delete('1');
        
        // //Save the User Data//////
        foreach ($userRoleData as $userRole) {
            $userRoleData = explode("_", $userRole);
            unset($userRole);
            $userRole['user_id'] = $userRoleData[0];
            $userRole['role_id'] = $userRoleData[1];
            try {
                
                $sql = new Sql($this->getAdapter());
                $insert = $sql->insert($this->table);
                $insert->values($userRole);
                $statement = $sql->prepareStatementForSqlObject($insert)->execute();
            } catch (\Exception $err) {
                throw $err;
            }
        }
        return true;
    }

    public function getCurrentRoles()
    {
        try {
            $userRoles = array();
            $sql = new Sql($this->getAdapter());
            $select = $sql->select()->from(array(
                'role' => $this->table
            ));
            $select->columns(array(
                'user_id',
                'role_id'
            ));
            $statement = $sql->prepareStatementForSqlObject($select);
            $roles = $this->resultSetPrototype->initialize($statement->execute())
                ->toArray();
            foreach ($roles as $value) {
                $userRoles[] = $value['user_id'] . '_' . $value['role_id'];
            }
            return $userRoles;
        } catch (\Exception $err) {
            throw $err;
        }
    }

    public function getCurrentUserRoles($where)
    {
        try {
            $userRoles = array();
            $sql = new Sql($this->getAdapter());
            $select = $sql->select()->from(array(
                'user_role' => $this->table
            ));
            $select->columns(array(
                'user_id',
                'role_id'
            ));
            $select->join('users', 'user_role.user_id = users.id', array(
                'first_name',
                'last_name'
            ), $select::JOIN_INNER);
            $select->join('role', 'rid = user_role.role_id', array(
                'role_name'
            ), $select::JOIN_INNER);
            if (! empty($where)) {
                $select->where($where);
            }
            $select->order(array(
                '0' => 'users.first_name',
                '1' => 'users.last_name'
            ));
            $sqlhhh = $select->getSqlString();
            $statement = $sql->prepareStatementForSqlObject($select);
            
            $roles = $this->resultSetPrototype->initialize($statement->execute())
                ->toArray();
            
            foreach ($roles as $k => $value) {
                $userRoles[$value['user_id']]['roleDetails'][] = $value['role_name'];
                $userRoles[$value['user_id']]['roleUser'] = $value['first_name'] . ' ' . $value['last_name'];
            }
           
            return $userRoles;
        } catch (\Exception $err) {
            throw $err;
        }
    }

    
    
    /**
     * Get user details
     *
     * @throws \Exception
     * @return multitype:string
     */
    public function getRoleUserDetails($where)
    {
    	try {
    		$userRoles = array();
    		$sql = new Sql($this->getAdapter());
    		$select = $sql->select()->from(array('users' => 'users'));
    		$select->columns(array('id','first_name','last_name'));
    		
    		if (! empty($where)) {
    			$select->where($where);
    		}
    		$select->order(array(
    				'0' => 'users.first_name',
    				'1' => 'users.last_name'
    		));
    		$sqlhhh = $select->getSqlString();
    		$statement = $sql->prepareStatementForSqlObject($select);
    
    		$roles = $this->resultSetPrototype->initialize($statement->execute())
    		->toArray();
    		sort($roles);
    		foreach ($roles as $k => $value) {
    			$userRoles['roleUser'] = $value['first_name'] . ' ' . $value['last_name'];
    		}
    		 
    		return $userRoles;
    	} catch (\Exception $err) {
    		throw $err;
    	}
    }
    
    public function getAllUsers()
    {
        try {
            $sql = new Sql($this->getAdapter());
            $select = $sql->select()->from(array(
                'users' => 'users'
            ));
            $select->columns(array(
                'id',
                'first_name',
                'last_name'
            ));
            $select->order(array(
                '0' => 'users.first_name',
                '1' => 'users.last_name'
            ));
            $statement = $sql->prepareStatementForSqlObject($select);
            $userArr = $this->resultSetPrototype->initialize($statement->execute())
                ->toArray();
            return $userArr;
        } catch (\Exception $err) {
            throw $err;
        }
    }

    public function getUserPermissions()
    {
        try {
            $sql = new Sql($this->getAdapter());
            $select = $sql->select()->from(array(
                'user_role' => $this->table
            ));
            $select->join('users', 'user_role.user_id = users.id', array(
                'first_name',
                'last_name'
            ), $select::JOIN_INNER);
            $select->join('role', 'rid = user_role.role_id', array(
                'role_name'
            ), $select::JOIN_INNER);
            $select->order(array(
                '0' => 'users.first_name',
                '1' => 'users.last_name'
            ));
            // $select->group('users.id');
            $statement = $sql->prepareStatementForSqlObject($select);
            $userRoles = $this->resultSetPrototype->initialize($statement->execute())
                ->toArray();
            return $userRoles;
        } catch (\Exception $err) {
            throw $err;
        }
    }

    public function saveClientRole($saveRole)
    {
        try {
            $this->insert($saveRole);
        } catch (\Exception $err) {
            prx($err->getPrevious()->getMessage());
        }
    }

    public function deleteRoles($where)
    {
        try {
            $sql = new Sql($this->getAdapter());
            
            $delete = new Delete($this->table);
            $delete->where($where);
            
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $affectedRows = $results->getAffectedRows();
        } catch (\Exception $err) {
            throw $err;
        }
        return $affectedRows;
    }
}
