<?php

namespace FraudHunter\Laravel;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

class FraudHunterClient
{
    /** @var Client */
    protected $http;

    /** @var array */
    protected $config;

    /** @var LoggerInterface|null */
    protected $logger;

    public function __construct(array $config, ?LoggerInterface $logger = null)
    {
        $this->config = $config;
        $this->logger = $logger;
        
        $this->http = new Client([
            'base_uri' => $config['api_url'],
            'timeout'  => 3.0, // Short timeout to avoid blocking WL app
            'headers'  => [
                'X-API-Key' => $config['api_key'],
                'Accept'     => 'application/json',
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    /**
     * Send transaction for analysis.
     *
     * @param array $data
     * @return array|null
     */
    public function analyzeTransaction(array $data)
    {
        return $this->post('/api/v1/transactions/analyze', $data);
    }

    /**
     * Log user activity.
     *
     * @param array $data
     * @return array|null
     */
    public function logActivity(array $data)
    {
        // Ensure service is set from config if not provided
        if (!isset($data['service'])) {
            $data['service'] = $this->config['service'];
        }

        return $this->post('/api/v1/activities', $data);
    }

    /**
     * Internal post helper with error handling.
     *
     * @param string $endpoint
     * @param array $data
     * @return array|null
     */
    protected function post($endpoint, array $data)
    {
        try {
            $response = $this->http->post($endpoint, [
                'json' => $data
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            if ($this->logger) {
                $this->logger->error('FraudHunter API Error: ' . $e->getMessage(), [
                    'endpoint' => $endpoint,
                    'data'     => $data,
                ]);
            }
            return null;
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error('FraudHunter Unexpected Error: ' . $e->getMessage());
            }
            return null;
        }
    }
}
