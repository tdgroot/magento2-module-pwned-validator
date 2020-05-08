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
     * @param string|null $password
     */
    private function dispatchPasswordCheckEvent($password)
    {
        if (!is_null($password)) {
            $this->eventManager->dispatch(
                'timpack_pwnedvalidator_check_password_strength',
                [
                    'password' => $password,
                ]
            );
        }
    }

    /**
     * @param AccountManagementInterface $subject
     * @param string $email
     * @param string $resetToken
     * @param string $newPassword
     */
    public function beforeResetPassword(
        AccountManagementInterface $subject,
        $email,
        $resetToken,
        $newPassword
    ) {
        $this->dispatchPasswordCheckEvent($newPassword);
    }

    /**
     * @param AccountManagementInterface $subject
     * @param string $email
     * @param string $currentPassword
     * @param string $newPassword
     */
    public function beforeChangePassword(
        AccountManagementInterface $subject,
        $email,
        $currentPassword,
        $newPassword
    ) {
        $this->dispatchPasswordCheckEvent($newPassword);
    }

    /**
     * @param AccountManagementInterface $subject
     * @param string $customerId
     * @param string $currentPassword
     * @param string $newPassword
     */
    public function beforeChangePasswordById(
        AccountManagementInterface $subject,
        $customerId,
        $currentPassword,
        $newPassword
    ) {
        $this->dispatchPasswordCheckEvent($newPassword);
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
        $this->dispatchPasswordCheckEvent($password);
    }
}
