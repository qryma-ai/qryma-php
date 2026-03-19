<?php

/**
 * Qryma API Client implementation.
 */

namespace Qryma;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Search options
 */
class SearchOptions
{
    public $lang = '';
    public $start = 0;
    public $safe = false;
    public $detail = false;

    public function __construct(array $options = [])
    {
        foreach ($options as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function toArray(): array
    {
        return [
            'lang' => $this->lang,
            'start' => $this->start,
            'safe' => $this->safe,
            'detail' => $this->detail,
        ];
    }
}

/**
 * Client configuration
 */
class ClientConfig
{
    public $apiKey;
    public $baseUrl = 'https://search.qryma.com';
    public $timeout = 30;

    public function __construct(array $config)
    {
        if (!isset($config['apiKey']) || empty(trim($config['apiKey']))) {
            throw new \InvalidArgumentException('API key must be provided');
        }
        $this->apiKey = $config['apiKey'];

        if (isset($config['baseUrl'])) {
            $this->baseUrl = $config['baseUrl'];
        }
        if (isset($config['timeout'])) {
            $this->timeout = $config['timeout'];
        }
    }
}

class QrymaClient
{
    /**
     * @var string Qryma API key
     */
    private $apiKey;

    /**
     * @var string Base URL for the Qryma API
     */
    private $baseUrl;

    /**
     * @var int Request timeout in seconds
     */
    private $timeout;

    /**
     * @var array Request headers
     */
    private $headers;

    /**
     * @var Client HTTP client
     */
    private $httpClient;

    /**
     * Client for interacting with the Qryma Search API.
     *
     * @param string $apiKey Qryma API key for authentication
     * @param string $baseUrl Base URL for the Qryma API (default: https://search.qryma.com)
     * @param int $timeout Request timeout in seconds (default: 30)
     * @throws \InvalidArgumentException If the API key is invalid
     */
    public function __construct(
        string $apiKey,
        string $baseUrl = 'https://search.qryma.com',
        int $timeout = 30
    ) {
        if (empty(trim($apiKey))) {
            throw new \InvalidArgumentException('API key must be provided');
        }

        $this->apiKey = $apiKey;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->timeout = $timeout;
        $this->headers = [
            'X-Api-Key' => $apiKey,
            'Content-Type' => 'application/json',
            'User-Agent' => 'qryma-php/' . Version::VERSION
        ];

        $this->httpClient = new Client();
    }

    /**
     * Perform a search using the Qryma API.
     *
     * @param string $query The search query string
     * @param array|SearchOptions $options Search options (optional)
     *
     * @return array Raw API response containing the search results
     *
     * @throws \RuntimeException If there's an error with the request or response processing
     */
    public function search(string $query, $options = []): array
    {
        if ($options instanceof SearchOptions) {
            $opts = $options;
        } else {
            $opts = new SearchOptions($options);
        }

        $url = $this->baseUrl . '/api/web';
        $payload = array_merge(
            ['query' => $query],
            $opts->toArray()
        );

        try {
            $response = $this->httpClient->post($url, [
                'headers' => $this->headers,
                'json' => $payload,
                'timeout' => $this->timeout
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \RuntimeException(
                    sprintf('API request failed: %d %s', $response->getStatusCode(), $response->getReasonPhrase())
                );
            }

            $data = json_decode($response->getBody(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException('Error decoding JSON response');
            }

            return $data;

        } catch (GuzzleException $e) {
            throw new \RuntimeException(sprintf('API request failed: %s', $e->getMessage()), 0, $e);
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Error processing search: %s', $e->getMessage()), 0, $e);
        }
    }
}

/**
 * Create a Qryma client instance.
 *
 * @param array $config Client configuration
 * @param string $config['apiKey'] Qryma API key for authentication
 * @param string $config['baseUrl'] Base URL for the Qryma API (optional)
 * @param int $config['timeout'] Request timeout in seconds (optional)
 * @return QrymaClient Qryma client instance
 *
 * @example
 * ```php
 * // To install: composer require qryma-ai/qryma-php
 * $client = qryma(['apiKey' => 'ak-********************']);
 * $client->search('ces', ['lang' => 'zh-CN']);
 * ```
 */
function qryma(array $config): QrymaClient
{
    $clientConfig = new ClientConfig($config);
    return new QrymaClient($clientConfig->apiKey, $clientConfig->baseUrl, $clientConfig->timeout);
}

