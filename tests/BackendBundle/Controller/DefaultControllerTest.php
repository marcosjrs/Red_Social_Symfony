<?php

namespace BackendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
        //Este test en el futuro se cambiará, es para hacer una prueba inicial
        $this->assertContains('Hello World, Marcos!', $client->getResponse()->getContent());
    }
}
