<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Community;
use App\Entity\Post;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testEmailManagement(): void
    {
        $email = 'test@example.com';
        $this->user->setEmail($email);
        
        $this->assertEquals($email, $this->user->getEmail());
        $this->assertEquals($email, $this->user->getUserIdentifier());
    }

    public function testPasswordManagement(): void
    {
        $password = 'hashedpassword123';
        $plainPassword = 'plainpassword123';
        
        $this->user->setPassword($password);
        $this->user->setPlainPassword($plainPassword);
        
        $this->assertEquals($password, $this->user->getPassword());
        $this->assertEquals($plainPassword, $this->user->getPlainPassword());
        
        $this->user->eraseCredentials();
        $this->assertNull($this->user->getPlainPassword());
    }

    public function testRolesManagement(): void
    {
        // Default roles now include ROLE_USER and ROLE_PARTICIPANT
        $expectedDefaultRoles = ['ROLE_USER', 'ROLE_PARTICIPANT'];
        $this->assertEquals($expectedDefaultRoles, $this->user->getRoles());
        
        $this->user->setRoles(['ROLE_ADMIN']);
        $roles = $this->user->getRoles();
        
        // Should contain all roles: ROLE_USER (always added), ROLE_PARTICIPANT (default), and ROLE_ADMIN (explicitly added)
        $this->assertContains('ROLE_USER', $roles);
        $this->assertContains('ROLE_PARTICIPANT', $roles);
        $this->assertContains('ROLE_ADMIN', $roles);
    }

    public function testCommunityManagement(): void
    {
        $community = new Community();
        
        $this->user->addCommunity($community);
        $this->assertCount(1, $this->user->getCommunities());
        
        $this->user->removeCommunity($community);
        $this->assertCount(0, $this->user->getCommunities());
    }

    public function testPostManagement(): void
    {
        $post = new Post();
        
        $this->user->addPost($post);
        $this->assertCount(1, $this->user->getPosts());
        
        $this->user->removePost($post);
        $this->assertCount(0, $this->user->getPosts());
    }

    public function testVerificationStatus(): void
    {
        $this->assertFalse($this->user->isVerified());
        
        $this->user->setVerified(true);
        $this->assertTrue($this->user->isVerified());
    }

    public function testAvatarManagement(): void
    {
        $avatarPath = 'path/to/avatar.jpg';
        
        $this->user->setAvatar($avatarPath);
        $this->assertEquals($avatarPath, $this->user->getAvatar());
    }
}
