<?php

namespace FraudHunter\Laravel\Listeners;

use FraudHunter\Laravel\FraudHunterClient;
use Illuminate\Support\Facades\Request;


class FraudHunterListener
{
    /** @var FraudHunterClient */
    protected $client;

    public function __construct(FraudHunterClient $client)
    {
        $this->client = $client;
    }

    public function handle($event)
    {
        $eventName = get_class($event);
        $map = config('fraudhunter.event_map', []);

        if (isset($map[$eventName])) {
            $type = $map[$eventName];
            
            if ($type === 'TRANSACTION') {
                $this->processTransaction($event);
            } else {
                $this->processActivity($type, $event);
            }
        }
    }

    /**
     * Process and send activity data to FraudHunter.
     *
     * @param string $activityType
     * @param mixed $event
     * @return void
     */
    protected function processActivity($activityType, $event)
    {
        $data = $this->getBaseData($event);
        $data['activity_type'] = $activityType;
        $data['status'] = 'SUCCESS';

        if (isset($data['account_id'])) {
            $this->client->logActivity($data);
        }
    }

    /**
     * Process and send transaction data for analysis.
     *
     * @param mixed $event
     * @return void
     */
    protected function processTransaction($event)
    {
        $data = $this->getBaseData($event);
        
        // Attempt to extract transaction data from standard property names
        $source = null;
        if (isset($event->transaction)) {
            $source = $event->transaction;
        } elseif (isset($event->order)) {
            $source = $event->order;
        } elseif (isset($event->data) && is_object($event->data)) {
            $source = $event->data;
        }

        if ($source) {
            $data['amount'] = $this->extract($source, ['amount', 'total', 'value']);
            $data['currency'] = $this->extract($source, ['currency', 'currency_code'], 'IDR');
            $data['ref_id'] = $this->extract($source, ['ref_id', 'reference', 'order_id', 'id']);
            $data['recipient_account_id'] = $this->extract($source, ['recipient_account_id', 'to_account', 'destination']);
            $data['transaction_type'] = $this->extract($source, ['transaction_type', 'type'], 'Transfer');
            $data['merchant'] = $this->extract($source, ['merchant', 'provider', 'vendor'], config('fraudhunter.service'));
        }

        if (isset($data['account_id']) && isset($data['amount'])) {
            $this->client->analyzeTransaction($data);
        }
    }

    /**
     * Get basic metadata for all events.
     *
     * @param mixed $event
     * @return array
     */
    protected function getBaseData($event)
    {
        $data = [
            'timestamp'  => now()->toIso8601String(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
            'device_id'  => Request::header('X-Device-ID') ?: session()->getId(),
        ];

        // Extract Account ID
        if (isset($event->user) && isset($event->user->id)) {
            $data['account_id'] = (string)$event->user->id;
        } elseif (auth()->check()) {
            $data['account_id'] = (string)auth()->id();
        }

        return $data;
    }

    /**
     * Helper to extract first available property from an object.
     *
     * @param object|array $source
     * @param array $keys
     * @param mixed $default
     * @return mixed
     */
    protected function extract($source, array $keys, $default = null)
    {
        foreach ($keys as $key) {
            if (is_object($source) && isset($source->$key)) {
                return $source->$key;
            } elseif (is_array($source) && isset($source[$key])) {
                return $source[$key];
            }
        }
        return $default;
    }
}
