<?php

namespace App\Tests\Entity;

use App\Entity\Manga;
use App\Entity\Post;
use App\Entity\Community;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\Collection;

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
        $imageUrl = 'https://example.com/image.jpg';
        
        $this->manga->setTitle($title);
        $this->manga->setDescription($description);
        $this->manga->setImageUrl($imageUrl);
        
        $this->assertEquals($title, $this->manga->getTitle());
        $this->assertEquals($description, $this->manga->getDescription());
        $this->assertEquals($imageUrl, $this->manga->getImageUrl());
    }

    public function testSlugGeneration(): void
    {
        $title = 'My Hero Academia';
        $expectedSlug = 'my-hero-academia';
        
        $this->manga->setTitle($title);
        
        $this->assertEquals($expectedSlug, $this->manga->getSlug());
    }

    public function testPostsManagement(): void
    {
        $post = new Post();
        
        $this->manga->addPost($post);
        $this->assertCount(1, $this->manga->getPosts());
        $this->assertTrue($this->manga->getPosts()->contains($post));
        
        $this->manga->removePost($post);
        $this->assertCount(0, $this->manga->getPosts());
        $this->assertFalse($this->manga->getPosts()->contains($post));
    }

    public function testCommunitiesManagement(): void
    {
        $community = new Community();
        
        $this->manga->addCommunity($community);
        $this->assertCount(1, $this->manga->getCommunities());
        $this->assertTrue($this->manga->getCommunities()->contains($community));
        
        $this->manga->removeCommunity($community);
        $this->assertCount(0, $this->manga->getCommunities());
        $this->assertFalse($this->manga->getCommunities()->contains($community));
    }

    public function testCreatedAtDefaultValue(): void
    {
        $this->assertNotNull($this->manga->getCreatedAt());
        $this->assertInstanceOf(\DateTimeInterface::class, $this->manga->getCreatedAt());
    }

    public function testUpdatedAtManagement(): void
    {
        $date = new \DateTime();
        $this->manga->setUpdatedAt($date);
        
        $this->assertEquals($date, $this->manga->getUpdatedAt());
    }

    public function testMangaStatus(): void
    {
        $this->manga->setStatus('ongoing');
        $this->assertEquals('ongoing', $this->manga->getStatus());
        
        $this->manga->setStatus('completed');
        $this->assertEquals('completed', $this->manga->getStatus());
    }

    public function testCollectionsInitialization(): void
    {
        $this->assertInstanceOf(Collection::class, $this->manga->getPosts());
        $this->assertInstanceOf(Collection::class, $this->manga->getCommunities());
        $this->assertCount(0, $this->manga->getPosts());
        $this->assertCount(0, $this->manga->getCommunities());
    }
}
