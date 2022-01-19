<?php
/**
 * Copyright © 2022 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CCCC\RedisCronJobStatus\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Predis\ClientFactory;
use Predis\Client;

class StatusPersistenceHelper extends AbstractHelper
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var ClientFactory
     */
    protected $redisClientFactory;

    /** @var ScopeConfigInterface  */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        ClientFactory $redisClientFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->redisClientFactory = $redisClientFactory;
        $this->scopeConfig = $scopeConfig;
    }

    public function setCronJobStarted($cronJobName)
    {
        $this->getRedisClient()->hset('cccc_cronjob', $cronJobName, 1);
    }

    public function setCronJobFinished($cronJobName)
    {
        $this->getRedisClient()->hset('cccc_cronjob', $cronJobName, 0);
    }

    public function setCronJobFailed($cronJobName)
    {
        $this->getRedisClient()->hset('cccc_cronjob', $cronJobName, 99);
    }

    protected function getRedisClient() : Client
    {
        if (!$this->client) {
            $this->client = $this->redisClientFactory->create(
                [
                    'parameters' => $this->scopeConfig->getValue('system/cron_redis/connection_string'),
                    'options' => ['database' => $this->scopeConfig->getValue('system/cron_redis/database')]
                ]
            );
        }

        return $this->client;
    }

}

