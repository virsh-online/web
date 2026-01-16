<?php
namespace App\Http\Handler;

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
        $collection = (new Virsh())->getCollection();
        $collection->addFilter(['enabled' => 1]);
        
        return
            $this->render(
                'landing', 
                ['collection' => $collection], 
                'poetry'
            );
    }
}
