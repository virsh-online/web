<?php
namespace App\Http\Handler\Admin\Poetry;

use App\Http\Handler\Admin\AdminHandler;
use App\Model\Virsh;
use Juzdy\Http\RequestInterface;
use Juzdy\Http\ResponseInterface;

class Toggle extends AdminHandler
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
                return $this->redirect('/admin/index&error=toggle_failed');
            }
        }
        
        return $this->redirect('/admin/index');
    }
}
