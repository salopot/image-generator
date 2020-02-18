<?php
namespace Salopot\ImageGenerator\ImageSources\Remote;

use Intervention\Image\Image;
use Salopot\ImageGenerator\ImageProvider;
use Salopot\ImageGenerator\ImageSources\ImageSourceInterface;
use Salopot\ImageGenerator\ImageSources\SourceSelectorTrait;

class PlaceKittenSource implements ImageSourceInterface
{
    use SourceSelectorTrait;

    public const NAME = 'PlaceKitten';

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @param ImageProvider $imageProvider
     */
    public function __construct(ImageProvider $imageProvider)
    {
        $this->imageProvider = $imageProvider;
    }

    protected function getRandomSelector()
    {
        return random_int(1, 16);
    }

    protected function getImageBySelector(int $width, int $height, $selector): Image
    {
        $url = "http://placekitten.com/{$width}/{$height}?image={$selector}";
        return $this->imageProvider->getImageManager()->make($url);
    }

    protected function getRandomImage(int $width, int $height): Image
    {
        return $this->getImageBySelector($width, $height, $this->getRandomSelector());
    }
}