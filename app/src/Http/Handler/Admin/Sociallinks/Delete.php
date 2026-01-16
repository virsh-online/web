<?php
namespace App\Http\Handler\Admin\SocialLinks;

use App\Http\Handler\Admin\AdminHandler;
use App\Model\SocialLink;
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
        
        if (!$id) {
            return $this->redirect('/?q=admin/sociallinks/index&error=no_id');
        }
        
        try {
            $socialLinkModel = new SocialLink();
            $socialLinkModel->load((int)$id);
            
            if (!$socialLinkModel->isLoaded()) {
                throw new \Exception('Посилання не знайдено');
            }
            
            $socialLinkModel->delete();
            
            return $this->redirect('/?q=admin/sociallinks/index');
            
        } catch (\Exception $e) {
            return $this->redirect('/?q=admin/sociallinks/index&error=delete_failed');
        }
    }
}
