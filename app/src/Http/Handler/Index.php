<?php
namespace App\Http\Handler;

use App\Model\SocialLink;
use App\Model\Virsh;
use Juzdy\Http\Handler;
use Juzdy\Http\RequestInterface;
use Juzdy\Http\ResponseInterface;

class Index extends Handler
{
    public function __construct() {}

    /**
     * {@inheritdoc}
     */
    public function handle(RequestInterface $request): ResponseInterface
    {
        $virshModel = new Virsh();
        $collection = $virshModel->getCollection();
        $collection->addFilter(['enabled' => 1]);
        
        // Get pagination parameters
        $page = max(1, (int)$request->query('page', 1));
        $perPage = 20;
        
        // Apply pagination
        $collection->setPageSize($perPage);
        
        // Get total count and pages for pagination
        $totalCount = $collection->count();
        $totalPages = $collection->getPages();
        
        // Validate page is within bounds
        $page = min($page, max(1, $totalPages));
        $collection->setPage($page);
        
        // Get social links for footer
        $socialLinkModel = new SocialLink();
        $socialLinks = $socialLinkModel->getCollection();
        $socialLinks->addFilter(['enabled' => 1]);
        $socialLinks->sort('sort_order', 'ASC');
        
        return
            $this->render(
                'landing', 
                [
                    'collection' => $collection,
                    'currentPage' => $page,
                    'totalPages' => $totalPages,
                    'totalCount' => $totalCount,
                    'socialLinks' => $socialLinks
                ], 
                'poetry'
            );
    }
}
