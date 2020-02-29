<?php
declare(strict_types=1);

namespace Salopot\ImageGenerator\ImageSources\Local;

use Salopot\ImageGenerator\ImageManager;
use Salopot\ImageGenerator\ImageProvider;
use Salopot\ImageGenerator\ImageSources\ImageSourceInterface;
use Salopot\ImageGenerator\ImageSources\NamedTrait;
use Salopot\ImageGenerator\ImageSources\SourceSelectorTrait;
use Intervention\Image\Image;
use InvalidArgumentException;

class GradientSource implements ImageSourceInterface
{
    use SourceSelectorTrait,
        NamedTrait;

    public const NAME = 'Gradient';

    public const DIRECTION_VERTICAL = 'vertical';
    public const DIRECTION_HORIZONTAL = 'horizontal';

    /**
     * SolidFillSource constructor.
     * @param ImageProvider $imageProvider
     */
    public function __construct(ImageProvider $imageProvider)
    {
        $this->imageProvider = $imageProvider;
    }

    protected function getRandomSelector()
    {
        $selector = [
            'startColor' => $this->imageProvider->getGenerator()->hexColor,
            'endColor' => $this->imageProvider->getGenerator()->hexColor,
            'direction' => $this->imageProvider->getGenerator()->randomElement([
                self::DIRECTION_VERTICAL,
                self::DIRECTION_HORIZONTAL,
            ]),
        ];
        return $selector;
    }

    protected function getImageBySelector(int $width, int $height, $selector): Image
    {
        switch ($this->imageProvider->getImageManager()->getDriver()) {
            case ImageManager::DRIVER_IMAGICK:
                return $this->gradientImagick(
                    $width,
                    $height,
                    $selector['startColor'],
                    $selector['endColor'],
                    $selector['direction']
                );
            case ImageManager::DRIVER_GD:
                return $this->gradientGD(
                    $width,
                    $height,
                    $selector['startColor'],
                    $selector['endColor'],
                    $selector['direction']
                );
        }
    }

    public function getRandomImage(int $width, int $height): Image
    {
        return $this->getImageBySelector($width, $height, $this->getRandomSelector());
    }

    /**
     * @see https://imagemagick.org/script/gradient.php
     * @param int $width
     * @param int $height
     * @param string $startColor
     * @param string $endColor
     * @param string $direction
     * @return Image
     */
    protected function gradientImagick(int $width, int $height, string $startColor, string $endColor, string $direction): Image
    {
        $imagick = new \Imagick();
        $pseudoString = "gradient:{$startColor}-{$endColor}";
        if ($direction === self::DIRECTION_VERTICAL) {
            $imagick->newPseudoImage($width, $height, $pseudoString);
        } else {
            $imagick->newPseudoImage($height, $width, $pseudoString);
            $imagick->rotateImage('#fff', 90);
        }
        $imagick->newPseudoImage($width, $height, $pseudoString);
        return $this->imageProvider->getImageManager()->make($imagick);
    }

    /**
     * Convert hex color to RGB array
     * @param string $color
     * @return array
     */
    protected function hexToRGB(string $color): array
    {
        if (strpos($color, '#') !== 0) {
            throw new InvalidArgumentException('Invalid color value');
        }
        $color = substr($color, 1);
        if(strlen($color) === 3) {
            $r = hexdec(str_repeat($color[0], 2));
            $g = hexdec(str_repeat($color[1], 2));
            $b = hexdec(str_repeat($color[2], 2));
        } else {
            $r = hexdec(substr($color,0,2));
            $g = hexdec(substr($color,2,2));
            $b = hexdec(substr($color,4,2));
        }
        return [
            'red' => $r,
            'green' => $g,
            'blue' => $b
        ];
    }

    /**
     *
     * @param int $width
     * @param int $height
     * @param string $startColor
     * @param string $endColor
     * @param string $direction
     */
    protected function gradientGd(int $width, int $height, string $startColor, string $endColor, string $direction)
    {
        $img = imagecreatetruecolor($width, $height);
        $startColor = $this->hexToRGB($startColor);
        $endColor = $this->hexToRGB($endColor);

        switch ($direction) {
            case self::DIRECTION_VERTICAL:
                $steps = $height;
                break;
            case self::DIRECTION_HORIZONTAL:
                $steps = $width;
                break;
        }
        $colorSteps = min($steps,254);
        $stepSize = $steps / $colorSteps;
        $colorStep = 0;

        for($i = 0; $i < $steps; $i++) {
            if ($i >= $colorStep * $stepSize) {
                $r = (int) ($startColor['red'] - (($startColor['red']-$endColor['red']) / $colorSteps)*$colorStep);
                $g = (int) ($startColor['green'] - (($startColor['green']-$endColor['green']) / $colorSteps)*$colorStep);
                $b = (int) ($startColor['blue'] - (($startColor['blue']-$endColor['blue']) / $colorSteps)*$colorStep);
                $color = imagecolorallocate($img, $r, $g, $b);
                $colorStep++;
            }
            switch ($direction) {
                case self::DIRECTION_VERTICAL:
                    imagefilledrectangle($img, 0, $i, $width-1, $i+1, $color);
                    break;
                case self::DIRECTION_HORIZONTAL:
                    imagefilledrectangle($img, $i, 0, $i+1, $height-1, $color);
                    break;
            }
        }
        return $this->imageProvider->getImageManager()->make($img);
    }
}
