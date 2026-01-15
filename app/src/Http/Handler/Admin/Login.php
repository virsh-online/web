<?php
namespace App\Http\Handler\Admin;

use Juzdy\Http\Handler;
use Juzdy\Http\RequestInterface;
use Juzdy\Http\ResponseInterface;

class Login extends Handler
{
    public function __construct() {}

    /**
     * {@inheritdoc}
     */
    public function handle(RequestInterface $request): ResponseInterface
    {
        $error = '';
        
        if ($request->isPost()) {
            $username = $request->post('username');
            $password = $request->post('password');
            
            // Simple authentication - in production, use proper password hashing
            // Default credentials: admin/admin
            if ($username === 'admin' && $password === 'admin') {
                $request->session('admin_user_id', 1);
                
                // Redirect to intended URL or admin index
                $intendedUrl = $request->session('intended_url') ?? '/?q=admin/index';
                $request->session('intended_url', null);
                
                return $this->redirect($intendedUrl);
            } else {
                $error = 'Невірний логін або пароль';
            }
        }
        
        return $this->render('login', ['error' => $error], 'admin');
    }
}
