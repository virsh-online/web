<?php
namespace App\Model;

use Juzdy\Model;

class Virsh extends Model
{
    protected string $table = 'virsh';

    public function getIllustrationUrl(): ?string
    {
        $illustration = $this->get('illustration');
        if ($illustration) {
            return '/'.$illustration;
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