<?php
namespace Salopot\ImageGenerator\ImageSources\Remote;

use Intervention\Image\Image;
use Salopot\ImageGenerator\ImageProvider;
use Salopot\ImageGenerator\ImageSources\ImageSourceInterface;
use Salopot\ImageGenerator\ImageSources\ResizeSelectorTrait;

class PlaceImgSource implements ImageSourceInterface
{
    use ResizeSelectorTrait;

    public const NAME = 'PlaceImg';

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

    protected function getRandomImage(int $width, int $height): Image
    {
        $random = random_int(11111, 99999);
        $url = "https://placeimg.com/{$width}/{$height}/any?{$random}";
        return $this->imageProvider->getImageManager()->make($url);
    }
}