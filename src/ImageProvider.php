<?php
declare(strict_types=1);

namespace Salopot\ImageGenerator;

use Salopot\ImageGenerator\ImageSources\ImageSourceInterface;
use Faker\Generator;
use Faker\Provider\Base as BaseProvider;
use RuntimeException;

class ImageProvider extends BaseProvider
{
    /** @var ImageSourceInterface[] */
    protected $imageSources;

    /** @var ImageManager */
    protected $imageManager;

    /**
     * ImageProvider constructor.
     * @param Generator $generator
     * @param string $driver Select image driver (auto | imagick | gd)
     */
    public function __construct(Generator $generator, string $driver = 'auto')
    {
        parent::__construct($generator);

        $this->imageManager = new ImageManager(array(
            'driver' => $driver,
        ));
    }

    /**
     * Return faker instance
     * @return Generator
     */
    public function getGenerator(): Generator
    {
        return $this->generator;
    }

    /**
     * Return image manager
     * @return ImageManager
     */
    public function getImageManager(): ImageManager
    {
        return $this->imageManager;
    }

    /**
     * Add new image source to list
     * @param ImageSourceInterface $imageSource
     */
    public function addImageSource(ImageSourceInterface $imageSource)
    {
        $this->imageSources[$imageSource->getName()] = $imageSource;
    }

    /**
     * Return image source by name (or default if null)
     * @param string|null $name
     * @return ImageSourceInterface
     */
    protected function getImageSource(?string $name = null): ImageSourceInterface
    {
        if ($name === null) {
            return reset($this->imageSources);
        }
        if (!isset($this->imageSources[$name])) {
            throw new RuntimeException("Not found source with name {$name}");
        }
        return $this->imageSources[$name];
    }

    /**
     * Return image generator
     * @param int $width
     * @param int $height
     * @param string|null $selector Group image by own selector or random if null
     * @param string|null $source Select source by name
     * @return ImageGenerator
     */
    public function imageGenerator(
        int $width,
        int $height,
        ?string $selector = null,
        ?string $source = null
    ): ImageGenerator {
        return new ImageGenerator(
            $this,
            $this->getImageSource($source)->getImage($width, $height, $selector)
        );
    }
}
