<?php

namespace Valsplat\Employes;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Valsplat\Employes\Exceptions\ApiException;
use Valsplat\Employes\Exceptions\NotFoundException;

/**
 * Class Connection.
 */
class Connection
{
    private string $apiUrl = 'https://connect.employes.nl/v2';
    private string $bearerToken;
    private string $administrationId;
    protected array $middleWares = [];
    private bool $testing = false;
    private Client $client;

    private function client(): Client
    {
        if (!empty($this->client)) {
            return $this->client;
        }

        $handlerStack = HandlerStack::create();
        foreach ($this->middleWares as $middleWare) {
            $handlerStack->push($middleWare);
        }

        $this->client = new Client([
            'http_errors' => true,
            'handler' => $handlerStack,
        ]);

        return $this->client;
    }

    /**
     * Insert a Middleware for the Guzzle Client.
     *
     * @param $middleWare
     */
    public function insertMiddleWare($middleWare)
    {
        $this->middleWares[] = $middleWare;
    }

    public function connect(string $token): Client
    {
        $this->setBearerToken($token);
        $client = $this->client();

        return $client;
    }

    /**
     * @param string $method
     * @param $endpoint
     * @param null $body
     *
     * @return Request
     */
    private function createRequest($method = 'GET', $endpoint, $body = null, array $params = [], array $headers = [])
    {
        // Add default json headers to the request
        $headers = array_merge($headers, [
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Bearer '.$this->getBearerToken(),
        ]);

        // Create param string
        if (!empty($params)) {
            $endpoint .= '?'.http_build_query($params);
        }

        return new Request($method, $endpoint, $headers, $body);
    }

    /**
     * @param $url
     *
     * @return mixed
     *
     * @throws ApiException
     */
    public function get($url, array $params = [])
    {
        try {
            $request = $this->createRequest('GET', $this->formatUrl($url, 'get'), null, $params);
            $response = $this->client()->send($request);

            return $this->parseResponse($response);
        } catch (Exception $e) {
            $this->parseExceptionForErrorMessages($e);
        }
    }

    public function post(string $url, $body)
    {
        try {
            $request = $this->createRequest('POST', $this->formatUrl($url, 'post'), $body);
            $response = $this->client()->send($request);

            return $this->parseResponse($response);
        } catch (Exception $e) {
            $this->parseExceptionForErrorMessages($e);
        }
    }

    public function patch(string $url, $body)
    {
        try {
            $request = $this->createRequest('PATCH', $this->formatUrl($url, 'patch'), $body);
            $response = $this->client()->send($request);

            return $this->parseResponse($response);
        } catch (Exception $e) {
            $this->parseExceptionForErrorMessages($e);
        }
    }

    public function delete(string $url)
    {
        try {
            $request = $this->createRequest('DELETE', $this->formatUrl($url, 'delete'));
            $response = $this->client()->send($request);

            return $this->parseResponse($response);
        } catch (Exception $e) {
            $this->parseExceptionForErrorMessages($e);
        }
    }

    public function getBearerToken(): string
    {
        return $this->bearerToken;
    }

    public function setBearerToken(string $bearerToken): void
    {
        $this->bearerToken = $bearerToken;
    }

    public function getAdministrationId(): string
    {
        return $this->administrationId;
    }

    public function setAdministrationId(string $administrationId): void
    {
        $this->administrationId = $administrationId;
    }

    /**
     * @return mixed
     *
     * @throws ApiException
     */
    private function parseResponse(Response $response)
    {
        try {
            Psr7\rewind_body($response);
            $json = json_decode($response->getBody()->getContents(), true);

            return $json;
        } catch (\RuntimeException $e) {
            throw new ApiException($e->getMessage());
        }
    }

    /**
     * Parse the reponse in the Exception to return the Exact error messages.
     *
     * @throws ApiException
     */
    private function parseExceptionForErrorMessages(Exception $e)
    {
        if (!$e instanceof BadResponseException) {
            throw new ApiException($e->getMessage());
        }

        $response = $e->getResponse();
        Psr7\rewind_body($response);
        $responseBody = $response->getBody()->getContents();
        $decodedResponseBody = json_decode($responseBody, true);

        if (!is_null($decodedResponseBody) && isset($decodedResponseBody['message'])) {
            $errorMessage = $decodedResponseBody['message'];
        } else {
            $errorMessage = $responseBody;
        }

        if ($response->getStatusCode() === 404) {
            throw new NotFoundException($errorMessage);
        }

        throw new ApiException('Error '.$response->getStatusCode().': '.$errorMessage, $response->getStatusCode());
    }

    /**
     * @param $url
     * @param string $method
     *
     * @return string
     */
    private function formatUrl($url, $method = 'get')
    {
        if ($this->testing) {
            return 'https://httpbin.org/'.$method;
        }

        return $this->apiUrl.'/'.$url;
    }

    /**
     * @return bool
     */
    public function isTesting()
    {
        return $this->testing;
    }

    /**
     * @param bool $testing
     */
    public function setTesting($testing)
    {
        $this->testing = $testing;
    }

    public function setApiUrl($apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }
}
