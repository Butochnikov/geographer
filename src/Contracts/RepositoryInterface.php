<?php

namespace MenaraSolutions\Geographer\Contracts;

/**
 * Interface RepositoryInterface
 * @package MenaraSolutions\Geographer\Contracts
 */
interface RepositoryInterface
{
    /**
     * @param string $class
     * @param array $params
     * @return array
     */
    public function getData($class, array $params);
}