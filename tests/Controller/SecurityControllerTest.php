<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testLoginPage()
    {
        $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    public function testLoginWithBadCredentials()
    {
        $crawler = $this->client->request('GET', '/login');
        
        $form = $crawler->filter('form')->form([
            '_username' => 'bad@email.com',
            '_password' => 'wrongpassword',
            '_csrf_token' => $crawler->filter('input[name="_csrf_token"]')->attr('value'),
        ]);

        $this->client->submit($form);
        
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testRegistrationPage()
    {
        $this->client->request('GET', '/register');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testLoginSuccessful()
    {
        $crawler = $this->client->request('GET', '/login');
        
        $form = $crawler->filter('form')->form();
        $this->client->submit($form, [
            '_username' => 'admin@test.com',
            '_password' => 'password123'
        ]);
        
        $this->assertResponseRedirects();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->client = null;
    }
}
