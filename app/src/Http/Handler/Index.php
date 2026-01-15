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
        return
            $this->render(
                'landing', 
                ['collection' => (new Virsh())->getCollection()], 
                'poetry'
            );
    }
}
