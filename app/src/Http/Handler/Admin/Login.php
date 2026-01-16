<?php
namespace App\Http\Handler\Admin;

use App\Model\AdminUser;
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
            $email = $request->post('username'); // Using 'username' field for email
            $password = $request->post('password');
            
            // Validate input
            if (empty($email) || empty($password)) {
                $error = 'Будь ласка, заповніть всі поля';
            } else {
                try {
                    // Find user by email
                    $adminUserModel = new AdminUser();
                    $user = $adminUserModel->findByEmail($email);
                    
                    // Verify user exists, is enabled, and password matches
                    if ($user && $user->isEnabled() && $user->verifyPassword($password)) {
                        // Set session with actual user ID
                        $request->session('admin_user_id', $user->get('id'));
                        
                        // Redirect to intended URL or admin index
                        $intendedUrl = $request->session('intended_url') ?? '/?q=admin/index';
                        $request->session('intended_url', null);
                        
                        return $this->redirect($intendedUrl);
                    } else {
                        $error = 'Невірний логін або пароль';
                    }
                } catch (\Exception $e) {
                    error_log('Login error: ' . $e->getMessage());
                    $error = 'Помилка авторизації. Спробуйте пізніше.';
                }
            }
        }
        
        return $this->render('login', ['error' => $error], 'admin');
    }
}
