<?php

namespace Timpack\PwnedValidator\Model;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\ClientInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Timpack\PwnedValidator\Api\ValidatorInterface;

class Validator implements ValidatorInterface
{
    const PWNED_BASE_URL = 'https://api.pwnedpasswords.com';
    const CONFIG_PWNED_MINIMUM_MATCHES = 'customer/pwned/minimum_matches';

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Validator constructor.
     * @param ClientInterface $httpClient
     * @param CacheInterface $cache
     * @param SerializerInterface $serializer
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ClientInterface $httpClient,
        CacheInterface $cache,
        SerializerInterface $serializer,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->httpClient = $httpClient;
        $this->cache = $cache;
        $this->serializer = $serializer;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $password
     * @return bool
     */
    public function isValid($password): bool
    {
        $passwordHash = strtoupper(sha1($password));
        $prefix = substr($passwordHash, 0, 5);
        $suffix = substr($passwordHash, 5);

        $minimumMatches = $this->getMinimumMatches();
        $hashes = $this->query($prefix);
        $count = $hashes[$suffix] ?? 0;

        return $count < $minimumMatches;
    }

    /**
     * @param $prefix
     * @return array
     */
    private function query($prefix): array
    {
        $cacheKey = 'PWNED_HASH_RANGE_' . $prefix;

        $cacheEntry = $this->cache->load($cacheKey);
        if ($cacheEntry) {
            return $this->serializer->unserialize($cacheEntry);
        }

        $hashes = [];

        $this->httpClient->get(self::PWNED_BASE_URL . '/range/' . $prefix);

        if ($this->httpClient->getStatus() !== 200) {
            return $hashes;
        }

        $body = $this->httpClient->getBody();
        $results = explode("\n", $body);

        foreach ($results as $value) {
            list($hash, $count) = explode(':', $value);
            $hashes[$hash] = (int)$count;
        }

        $serialized = $this->serializer->serialize($hashes);
        $this->cache->save($serialized, $cacheKey, [], 3600 * 8);

        return $hashes;
    }

    /**
     * @return int
     */
    private function getMinimumMatches(): int
    {
        return (int)$this->scopeConfig->getValue(self::CONFIG_PWNED_MINIMUM_MATCHES, 'stores');
    }
}
