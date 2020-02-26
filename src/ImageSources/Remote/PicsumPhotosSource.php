<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Salopot\ImageGenerator\ImageSources\Remote;

use Salopot\ImageGenerator\ImageProvider;
use Salopot\ImageGenerator\ImageSources\ImageSourceInterface;
use Salopot\ImageGenerator\ImageSources\NamedTrait;
use Salopot\ImageGenerator\ImageSources\SourceSelectorTrait;
use Intervention\Image\Image;

class PicsumPhotosSource implements ImageSourceInterface
{
    use SourceSelectorTrait,
        NamedTrait;

    public const NAME = 'PicsumPhotos';

    /**
     * PicsumPhotosSource constructor.
     * @param ImageProvider $imageProvider
     */
    public function __construct(ImageProvider $imageProvider)
    {
        $this->imageProvider = $imageProvider;
    }

    protected function getRandomSelector()
    {
        return mt_rand(11111, 99999);
    }

    protected function getImageBySelector(int $width, int $height, $selector): Image
    {
        $url = "https://picsum.photos/seed/{$selector}/{$width}/{$height}.jpg";
        return $this->imageProvider->getImageManager()->make($url);
    }

    protected function getRandomImage(int $width, int $height): Image
    {
        $url = "https://picsum.photos/{$width}/{$height}.jpg";
        return $this->imageProvider->getImageManager()->make($url);
    }
}
