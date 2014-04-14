<?php

namespace ZF2AuthAcl\Utility;

use Zend\Crypt\Password\Bcrypt;

class UserPassword
{

    public $salt = 'aUJGgadjasdgdj';

    public $method = 'sha1';

    /**
     * Constructor
     *
     * @author Kaushal Kishore
     * @access public
     *        
     * @param string $method
     *            // Encryption method
     * @return void
     */
    public function __construct($method = null)
    {
        if (! is_null($method)) {
            $this->method = $method;
        }
    }

    /**
     * Create Password
     *
     * @author Kaushal Kishore
     * @access public
     *        
     * @param string $password
     *            User Password
     * @return string
     */
    public function create($password)
    {
        if ($this->method == 'md5') {
            return md5($this->salt . $password);
        } elseif ($this->method == 'sha1') {
            return sha1($this->salt . $password);
        } elseif ($this->method == 'bcrypt') {
            $bcrypt = new Bcrypt();
            $bcrypt->setCost(14);
            return $bcrypt->create($password);
        }
    }

    /**
     * Validate the user password
     *
     * @author Kaushal Kishore
     * @access public
     *        
     * @param string $password
     *            // Password string
     *            
     * @param string $hash
     *            // Hash string
     *            
     * @return boolean
     */
    public function verify($password, $hash)
    {
        if ($this->method == 'md5') {
            return $hash == md5($this->salt . $password);
        } elseif ($this->method == 'sha1') {
            return $hash == sha1($this->salt . $password);
        } elseif ($this->method == 'bcrypt') {
            $bcrypt = new Bcrypt();
            $bcrypt->setCost(14);
            return $bcrypt->verify($password, $hash);
        }
    }
}
