<?php
namespace App\Http\Handler\Admin\SocialLinks;

use App\Http\Handler\Admin\AdminHandler;
use App\Model\SocialLink;
use Juzdy\Http\RequestInterface;
use Juzdy\Http\ResponseInterface;

class Index extends AdminHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(RequestInterface $request): ResponseInterface
    {
        $socialLinkModel = new SocialLink();
        $collection = $socialLinkModel->getCollection();
        
        // Order by sort_order
        $collection->sort('sort_order', 'ASC');
        
        // Get error parameter from request
        $error = $request->query('error');
        
        return $this->render('sociallinks/index', [
            'collection' => $collection,
            'error' => $error
        ], 'admin');
    }
}
