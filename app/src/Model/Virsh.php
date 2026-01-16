<?php
namespace App\Model;

use Juzdy\Config;
use Juzdy\Model;

class Virsh extends Model
{
    protected string $table = 'virsh';

    public function getIllustrationUrl(): ?string
    {
        $illustration = $this->get('illustration');
        if ($illustration) {
            return Config::get('url.uploads') . '/' . $illustration;
        }
        return null;
    }

    public function getYoutube(): ?string
    {
        $youtube = $this->get('youtube');
        if ($youtube) {
            return 'https://www.youtube.com/embed/' . $youtube;
        }
        
        return null;
    }
}