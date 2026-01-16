<?php
namespace App\Http\Handler\Admin;

use Juzdy\Http\Handler;
use Juzdy\Http\RequestInterface;
use Juzdy\Http\ResponseInterface;

class Logout extends Handler
{
    public function __construct() {}

    /**
     * {@inheritdoc}
     */
    public function handle(RequestInterface $request): ResponseInterface
    {
        $request->clearSession();
        
        // Redirect to login page
        return $this->redirect('/admin/login');
    }
}
