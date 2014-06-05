<?php

namespace AcceptanceTests;

use PHPUnit_Framework_TestCase;
use Guzzle\Http\Client as HttpClient;
use Guzzle\Http\Message\Response;

/**
 * Class TestCase
 *
 * @package Tests\Acceptance
 */
class TestCase extends PHPUnit_Framework_TestCase
{
    /** @var HttpClient */
    private $client;

    /** @var string */
    private $baseUrl;

    /**
     * @return string
     */
    protected function getBaseUrl()
    {
        if ($this->baseUrl === null) {
            $this->baseUrl = 'http://' . WEB_SERVER_HOST . ':' . WEB_SERVER_PORT;
        }

        return $this->baseUrl;
    }

    /**
     * @return HttpClient
     */
    public function getClient()
    {
        if (null === $this->client) {
            $this->client = new HttpClient();
        }

        return $this->client;
    }

    /**
     * @param string $url
     * @param array  $postBody
     *
     * @return Response
     */
    public function post($url, array $postBody = array())
    {
        try {
            $requestUrl = $this->getBaseUrl() . $url;
            $request = $this->getClient()->post($requestUrl, null, $postBody);
            $response = $this->getClient()->send($request);

        } catch (\Exception $exc) {
            $this->tearDown();
            $this->fail($exc->getMessage());
            $response = null;
        }

        return $response;
    }

    /**
     * @param string $url
     *
     * @return Response
     */
    public function get($url)
    {
        try {
            $requestUrl = $this->getBaseUrl() . $url;
            $request = $this->getClient()->get($requestUrl);
            $response = $this->getClient()->send($request);
        } catch (\Exception $exc) {
            $this->tearDown();
            $this->fail($exc->getMessage());
            $response = null;
        }

        return $response;
    }

    /**
     * @param string $url
     *
     * @return Response
     */
    public function delete($url)
    {
        try {
            $requestUrl = $this->getBaseUrl() . $url;
            $request = $this->getClient()->delete($requestUrl);
            $response = $this->getClient()->send($request);
        } catch (\Exception $exc) {
            $this->tearDown();
            $this->fail($exc->getMessage());
            $response = null;
        }

        return $response;
    }

    /**
     * @return array
     */
    public function registerNewUser()
    {
        $email    = uniqid('email_') . '@email.com';
        $password = uniqid('password');

        $postData = array(
            'username'     => $email,
            'password'     => $password,
            'timezone'     => 'Europe/Berlin',
            'display_name' => 'Test User',
            'language'     => 'en_EN',
            'locale'       => 'en_EN',
        );

        $response = $this->post('/user/register', $postData);
        $data = (array)$response->json();

        $this->assertTrue($data['success']);
        $this->assertGreaterThan(0, $data['data']['id']);
        $this->assertEquals(\Api\Entities\User::STATE_ACTIVE, $data['data']['state']);
        $this->assertEquals($postData['username'], $data['data']['email']);
        $this->assertEquals('user', $data['data']['role']);

        $user = array_merge($data['data'], ['password' => $password]);

        return $user;
    }

    /**
     * @param array $user
     *
     * @return string token
     */
    public function login(array $user)
    {
        $postData = array(
            'username' => $user['email'],
            'password' => $user['password']
        );

        $response = $this->post('/authenticate/login', $postData);
        $responseData = (array)$response->json();

        $this->assertTrue($responseData['success']);
        $this->assertNotEmpty($responseData['data']['token']);
        $this->assertEquals($user['id'], $responseData['data']['id']);

        return $responseData['data']['token'];
    }

    public function tearDown()
    {
        $configData = unserialize(TEST_CONFIG);

        $errorFile = basename($configData['log']['file']);
        $errorLog = realpath(__DIR__ . '/../../') . '/' . $errorFile;

        if (file_exists($errorLog)) {
            $contents = file_get_contents($errorLog);
            $this->assertEmpty($contents, $contents);
        }
    }
}