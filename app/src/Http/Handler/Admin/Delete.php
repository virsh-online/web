<?php
namespace App\Http\Handler\Admin;

use App\Model\Virsh;
use App\Http\Middleware\AuthMiddleware;
use Juzdy\Http\Handler;
use Juzdy\Http\RequestInterface;
use Juzdy\Http\ResponseInterface;

class Delete extends Handler
{
    public function __construct() {}

    /**
     * Register middleware for authentication
     */
    protected function registerMiddleware(): void
    {
        $this->addMiddleware(new AuthMiddleware());
    }

    /**
     * {@inheritdoc}
     */
    public function handle(RequestInterface $request): ResponseInterface
    {
        $id = $request->query('id');
        
        if ($id) {
            try {
                $virshModel = new Virsh();
                $virshModel->delete((int)$id);
            } catch (\Exception $e) {
                // Handle error silently or log it
            }
        }
        
        return $this->redirect('/?q=admin/index');
    }
}
