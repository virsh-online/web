<?php
namespace App\Http\Handler\Admin;

use App\Model\Virsh;
use Juzdy\Http\RequestInterface;
use Juzdy\Http\ResponseInterface;

class Index extends AdminHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(RequestInterface $request): ResponseInterface
    {
        $virshModel = new Virsh();
        $collection = $virshModel->getCollection();
        
        // Get error parameter from request
        $error = $request->query('error');
        
        return $this->render('index', [
            'collection' => $collection,
            'error' => $error
        ], 'admin');
    }
}
