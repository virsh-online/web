<?php
namespace App\Http\Handler\Admin;

use App\Model\Virsh;
use App\Http\Middleware\AuthMiddleware;
use Juzdy\Http\Handler;
use Juzdy\Http\RequestInterface;
use Juzdy\Http\ResponseInterface;

class Toggle extends Handler
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
                $virshModel->load((int)$id);
                
                if (!$virshModel->isLoaded()) {
                    throw new \Exception('Poem not found');
                }
                
                // Toggle enabled status
                $currentStatus = $virshModel->get('enabled');
                $virshModel->set('enabled', $currentStatus ? 0 : 1);
                $virshModel->save();
            } catch (\Exception $e) {
                // Log error for debugging
                error_log('Failed to toggle poem ID ' . $id . ': ' . $e->getMessage());
                // Redirect with error parameter
                return $this->redirect('/?q=admin/index&error=toggle_failed');
            }
        }
        
        return $this->redirect('/?q=admin/index');
    }
}
