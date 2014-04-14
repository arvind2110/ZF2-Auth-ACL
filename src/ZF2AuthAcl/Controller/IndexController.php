<?php
namespace ZF2AuthAcl\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZF2AuthAcl\Form\LoginForm;
use ZF2AuthAcl\Form\Filter\LoginFilter;
use ZF2AuthAcl\Utility\UserPassword;
use Zend\Session\Container;

class IndexController extends AbstractActionController
{

    public function indexAction()
    {
        $request = $this->getRequest();
        
        $view = new ViewModel();
        $loginForm = new LoginForm('loginForm');
        $loginForm->setInputFilter(new LoginFilter());
        
        if ($request->isPost()) {
            $data = $request->getPost();
            $loginForm->setData($data);
            
            if ($loginForm->isValid()) {
                $data = $loginForm->getData();
                
                $userPassword = new UserPassword();
                $encyptPass = $userPassword->create($data['password']);
                
                $authService = $this->getServiceLocator()->get('AuthService');
                
                $authService->getAdapter()
                    ->setIdentity($data['email'])
                    ->setCredential($encyptPass);
                
                $result = $authService->authenticate();
                
                if ($result->isValid()) {
                    
                    $userDetails = $this->_getUserDetails(array(
                        'email' => $data['email']
                    ), array(
                        'user_id'
                    ));
                    
                    $session = new Container('User');
                    $session->offsetSet('email', $data['email']);
                    $session->offsetSet('userId', $userDetails[0]['user_id']);
                    $session->offsetSet('roleId', $userDetails[0]['role_id']);
                    $session->offsetSet('roleName', $userDetails[0]['role_name']);
                    
                    $this->flashMessenger()->addMessage(array(
                        'success' => 'Login Success.'
                    ));
                    // Redirect to page after successful login
                } else {
                    $this->flashMessenger()->addMessage(array(
                        'error' => 'invalid credentials.'
                    ));
                    // Redirect to page after login failure
                }
                return $this->redirect()->tourl('/login');
                // Logic for login authentication
            } else {
                $errors = $loginForm->getMessages();
                // prx($errors);
            }
        }
        
        $view->setVariable('loginForm', $loginForm);
        return $view;
    }

    public function logoutAction(){
        $authService = $this->getServiceLocator()->get('AuthService');
        
        $session = new Container('User');
        $session->getManager()->destroy();
        
        $authService->clearIdentity();
        return $this->redirect()->toUrl('/login');
    }
    
    private function _getUserDetails($where, $columns)
    {
        $userTable = $this->getServiceLocator()->get("UserTable");
        $users = $userTable->getUsers($where, $columns);
        return $users;
    }
}