<?php
namespace Larabase54\Repository;

use Larabase54\Repository\NoSQL\RedisRepository;
use Larabase54\Repository\SQL\MySqlRepository;

class Repository
{
    const TAG = '[RedisRepository]';
    
    private $mysqlAccessObject;
    private $redisAccessObject;
    
    public function __construct(Repository $repository)
    {
        $this->mysqlAccessObject = new MySqlRepository();
        $this->redisAccessObject = new RedisRepository();
    }
    
    public function setData()
    {
        $this->redisAccessObject->setData('test');
    }
}