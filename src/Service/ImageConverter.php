<?php

namespace App\Service;

use Imagick;
use ImagickPixel;

class ImageConverter
{
    public function convertSVGtoPNG(string $svg, string $path): void
    {
        $im = new Imagick();
        $im->setBackgroundColor(new ImagickPixel('transparent'));
        $im->readImageBlob($svg);
        $im->setImageFormat("png32");
        $im->writeImage($path);
        $im->clear();
        $im->destroy();
    }
}