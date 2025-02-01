<?php

namespace App\Tests\Entity;

use App\Entity\Manga;
use PHPUnit\Framework\TestCase;

class MangaTest extends TestCase
{
    private Manga $manga;

    protected function setUp(): void
    {
        $this->manga = new Manga();
    }

    public function testBasicProperties(): void
    {
        $title = 'One Piece';
        $description = 'A story about pirates';
        
        $this->manga->setTitle($title);
        $this->manga->setDescription($description);
        
        $this->assertEquals($title, $this->manga->getTitle());
        $this->assertEquals($description, $this->manga->getDescription());
    }

    public function testStatusManagement(): void
    {
        $status = 'ongoing';
        $this->manga->setStatus($status);
        $this->assertEquals($status, $this->manga->getStatus());
    }

    public function testTitleIsRequired(): void
    {
        $manga = new Manga();
        $this->assertNull($manga->getTitle());
        
        $title = 'Test Manga';
        $manga->setTitle($title);
        $this->assertEquals($title, $manga->getTitle());
    }

    public function testDescriptionCanBeNull(): void
    {
        $manga = new Manga();
        $this->assertNull($manga->getDescription());
        
        $description = 'Test description';
        $manga->setDescription($description);
        $this->assertEquals($description, $manga->getDescription());
    }
}
