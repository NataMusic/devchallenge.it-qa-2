<?php

/*
 * This file is part of the OpenWeatherMap project.
 */

namespace end2end;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * Base class for API tests.
 */
class APITestCase extends TestCase
{
    const APPID = '76b9fa5e1638d02d4e803e4cb6416528';
    const API_URL = 'http://api.openweathermap.org/data/2.5/';
    const HEADERS = ['Content-Type' => 'application/json'];

    /** @var \GuzzleHttp\Client */
    private $client;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();
        $this->client = new Client(['base_uri' => self::API_URL]);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param string $body
     *
     * @return mixed|null|\Psr\Http\Message\ResponseInterface
     */
    public function request($method, $uri, $body = '')
    {
        try {
            $response = $this->client->request(
                $method,
                $uri.'&APPID='.self::APPID,
                ['body' => $body, 'headers' => self::HEADERS]
            );
        } catch (RequestException $exception) {
            $response = $exception->getResponse();
        }

        return $response;
    }

    /**
     * @param ResponseInterface $response
     * @param int               $statusCode
     * @param string            $message
     */
    public function assertStatusCode(ResponseInterface $response, $statusCode, $message = 'Wrong status code')
    {
        $this->assertSame($statusCode, $response->getStatusCode(), $message);
    }

    /**
     * @param $response
     *
     * @return mixed
     * @throws \RuntimeException
     */
    protected function getContent(ResponseInterface $response)
    {
        return json_decode($response->getBody()->getContents(), true);
    }
}
