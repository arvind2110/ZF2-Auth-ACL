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
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;

class UserRole extends AbstractTableGateway
{

    public $table = 'user_role';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet(ResultSet::TYPE_ARRAY);
        $this->initialize();
    }
    
    public function addNewuserRole($data= array())
    {
        $this->insert($data);
    }
    
    public function getUserRole($where = array())
    {
        try {
            $sql = new Sql($this->getAdapter());
            $select = $sql->select()->from(array(
                'userRole' => $this->table
            ));
            $select->columns(array(
                'user_id',
            ));
            
            $select->join(array('role' => 'role'), new Expression('userRole.role_id = role.rid'),
            array(
            	'rid',
                'role_name',
                'status',
            ),'RIGHT');
            if (count($where) > 0) {
                $select->where($where);
            }
            $select->group(array('role.rid'));
            $statement = $sql->prepareStatementForSqlObject($select);
            $roles = $this->resultSetPrototype->initialize($statement->execute())
            ->toArray();
            return $roles;
        } catch (\Exception $e) {
            throw new \Exception($e->getPrevious()->getMessage());
        }
    }

}
