<?php
namespace ZF2AuthAcl\Plugin\View;

use Zend\View\Helper\AbstractHelper;
use ZF2AuthAcl\Plugin\userAuthRole;

class userAuthRoleHelper extends AbstractHelper
{

    public function __invoke()
    {
        return new userAuthRole();
        
    }
}