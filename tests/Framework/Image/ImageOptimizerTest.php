<?php

namespace Cocktales\Framework\Image;

use Cocktales\Testing\Traits\UsesContainer;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageOptimizerTest extends TestCase
{
    use UsesContainer;

    /** @var  ImageOptimizer */
    private $optimizer;

    public function setUp()
    {
        $manager = $this->createContainer()->get(ImageManager::class);
        $this->optimizer = new ImageOptimizer($manager);
    }

    public function test_create_thumbnail_creates_a_100_x_125_size_image_from_uploaded_file()
    {
        $file = new UploadedFile('./src/public/img/default_avatar.jpg', 'default_avatar.jpg', 'image/jpeg', 22000, UPLOAD_ERR_OK, true);

        $image = $this->optimizer->createThumbnail($file);

        $this->assertInstanceOf(Image::class, $image);
        $this->assertEquals(125, $image->getHeight());
        $this->assertEquals(100, $image->getWidth());
        $this->assertEquals('default_avatar', $image->filename);
        $this->assertEquals('jpg', $image->extension);
    }

    public function test_create_thumbnail_creates_a_375_x_450_size_image_from_uploaded_file()
    {
        $file = new UploadedFile('./src/public/img/default_avatar.jpg', 'default_avatar.jpg', 'image/jpeg', 22000, UPLOAD_ERR_OK, true);

        $image = $this->optimizer->createStandardSize($file);

        $this->assertInstanceOf(Image::class, $image);
        $this->assertEquals(450, $image->getHeight());
        $this->assertEquals(375, $image->getWidth());
        $this->assertEquals('default_avatar', $image->filename);
        $this->assertEquals('jpg', $image->extension);
    }
}
