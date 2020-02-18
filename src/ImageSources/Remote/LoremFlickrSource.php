<?php
declare(strict_types=1);

namespace Salopot\ImageGenerator\ImageSources\Remote;

use Intervention\Image\Image;
use Salopot\ImageGenerator\ImageProvider;
use Salopot\ImageGenerator\ImageSources\ImageSourceInterface;
use Salopot\ImageGenerator\ImageSources\SourceSelectorTrait;

class LoremFlickrSource implements ImageSourceInterface
{
    use SourceSelectorTrait;

    public const NAME = 'LoremFlickr';

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * LoremFlickrSource constructor.
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
        $url = "https://loremflickr.com/{$width}/{$height}/all?lock={$selector}";
        return $this->imageProvider->getImageManager()->make($url);
    }

    protected function getRandomImage(int $width, int $height): Image
    {
        $url = "https://loremflickr.com/{$width}/{$height}/all";
        return $this->imageProvider->getImageManager()->make($url);
    }
}
