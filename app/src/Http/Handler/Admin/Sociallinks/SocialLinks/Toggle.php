<?php
namespace App\Http\Handler\Admin\SocialLinks;

use App\Http\Handler\Admin\AdminHandler;
use App\Model\SocialLink;
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
        
        if (!$id) {
            return $this->redirect('/?q=admin/sociallinks/index&error=no_id');
        }
        
        try {
            $socialLinkModel = new SocialLink();
            $socialLinkModel->load((int)$id);
            
            if (!$socialLinkModel->isLoaded()) {
                throw new \Exception('Посилання не знайдено');
            }
            
            // Toggle enabled status
            $currentStatus = $socialLinkModel->get('enabled');
            $socialLinkModel->setData(['enabled' => $currentStatus ? 0 : 1])->save();
            
            return $this->redirect('/?q=admin/sociallinks/index');
            
        } catch (\Exception $e) {
            return $this->redirect('/?q=admin/sociallinks/index&error=toggle_failed');
        }
    }
}
