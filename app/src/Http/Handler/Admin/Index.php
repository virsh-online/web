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
        
        // Get pagination parameters
        $page = max(1, (int)$request->query('page', 1));
        $perPage = 20;
        
        // Apply pagination
        $collection->setPageSize($perPage);
        $collection->setPage($page);
        
        // Get total count for pagination
        $totalPages = $collection->getPages();
        $totalCount = $collection->count();
        
        // Get error parameter from request
        $error = $request->query('error');
        
        return $this->render('index', [
            'collection' => $collection,
            'error' => $error,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCount' => $totalCount
        ], 'admin');
    }
}
