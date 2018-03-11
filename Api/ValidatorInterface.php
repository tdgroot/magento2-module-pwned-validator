<?php

namespace Timpack\PwnedValidator\Api;

interface ValidatorInterface
{
    /**
     * @param $password
     * @return bool
     */
    public function isValid($password): bool;
}
