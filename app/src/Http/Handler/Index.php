<?php
namespace App\Http\Handler;

use App\Model\SocialLink;
use App\Model\Virsh;
use Juzdy\Config;
use Juzdy\Http\Handler;
use Juzdy\Http\RequestInterface;
use Juzdy\Http\ResponseInterface;

class Index extends Handler
{

    /**
     * {@inheritdoc}
     */
    public function handle(RequestInterface $request): ResponseInterface
    {
        $virshModel = new Virsh();
        $collection = $virshModel->getCollection();
        $collection->addFilter(['enabled' => 1]);
        $collection->sort('created_at', 'DESC');
        
        // Get pagination parameters
        $page = max(1, (int)$request->query('page'));
        $perPage = 20;
        
        // Apply pagination
        $collection->setPageSize($perPage);
        $collection->setPage($page);
        
        // Get total count and pages for pagination
        $totalCount = $collection->count();
        $totalPages = $collection->getPages();
        
        // Get social links for footer
        $socialLinkModel = new SocialLink();
        $socialLinks = $socialLinkModel->getCollection();
        $socialLinks->addFilter(['enabled' => 1]);
        $socialLinks->sort('sort_order', 'ASC');

        $this->getLayout()->asset('css', [
            'href' => Config::get('url.asset') . '/css/main.css'
        ]);

        return $this->layout(
                'poetry',
                'landing',
                [
                    'collection' => $collection,
                    'currentPage' => $page,
                    'totalPages' => $totalPages,
                    'totalCount' => $totalCount,
                    'socialLinks' => $socialLinks
                ]
            );
    }
}
