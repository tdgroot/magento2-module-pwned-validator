<?php

namespace Timpack\PwnedValidator\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;
use Timpack\PwnedValidator\Api\ValidatorInterface;

class Validate implements ObserverInterface
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * Validate constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws InputException
     */
    public function execute(Observer $observer)
    {
        $password = $observer->getData('password');
        if (!$this->validator->isValid($password)) {
            throw new InputException(__('The password was found in public databases.'));
        }
    }
}
