<?php
declare(strict_types = 1);

namespace Shanla\Tools;


use Predis\Client;

class RedisTool
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'scheme' => 'tcp',
            'host' => '127.0.0.1',
            'port' => '6379'
        ]);
    }


}