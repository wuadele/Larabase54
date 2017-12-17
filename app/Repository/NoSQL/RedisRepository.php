<?php
namespace Larabase54\Repository\NoSQL;

use Predis\Client;

class RedisRepository extends AbstractRedisRepository
{
    const TAG = '[RedisRepository]';
    
    private $client;
    
    public function __construct()
    {
        $this->client = new Client(config('database.redis.default'));
    }
    
    public function setData($data)
    {
        $this->client->set('test_key', $data);
    }
}