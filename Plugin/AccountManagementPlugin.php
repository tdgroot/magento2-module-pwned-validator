<?php

namespace Timpack\PwnedValidator\Plugin;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Event\ManagerInterface;

class AccountManagementPlugin
{
    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * AccountManagementPlugin constructor.
     * @param ManagerInterface $eventManager
     */
    public function __construct(
        ManagerInterface $eventManager
    ) {
        $this->eventManager = $eventManager;
    }

    /**
     * @param AccountManagementInterface $subject
     * @param CustomerInterface $customer
     * @param null $password
     * @param string $redirectUrl
     */
    public function beforeCreateAccount(
        AccountManagementInterface $subject,
        CustomerInterface $customer,
        $password = null,
        $redirectUrl = ''
    ) {
        if (!is_null($password)) {
            $this->eventManager->dispatch(
                'timpack_pwnedvalidator_check_password_strength',
                [
                    'password' => $password,
                ]
            );
        }
    }
}
