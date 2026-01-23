<?php
namespace App\Http\Handler\Admin;

use Juzdy\Config;
use Juzdy\Http\Handler;

class AdminHandler extends Handler implements AuthintecableInterface
{
    public function __construct()
    {
        $this->getLayout()->asset('css', [
            'href' => Config::get('url.asset') . '/css/admin/main.css'
        ]);
    }
}
