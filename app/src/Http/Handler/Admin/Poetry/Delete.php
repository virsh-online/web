<?php
namespace App\Http\Handler\Admin\Poetry;

use App\Http\Handler\Admin\AdminHandler;
use App\Model\Virsh;
use Juzdy\Http\RequestInterface;
use Juzdy\Http\ResponseInterface;

class Delete extends AdminHandler
{
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
                // Log error for debugging
                error_log('Failed to delete poem ID ' . $id . ': ' . $e->getMessage());
                // Redirect with error parameter
                return $this->redirect('/?q=admin/index&error=delete_failed');
            }
        }
        
        return $this->redirect('/admin/index');
    }
}
