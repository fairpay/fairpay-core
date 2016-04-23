<?php


namespace Fairpay\Bundle\VendorBundle\Manager;


use Fairpay\Bundle\UserBundle\Event\UserCreatedEvent;
use Fairpay\Bundle\UserBundle\Manager\UserManager;
use Fairpay\Bundle\VendorBundle\Form\VendorData;

class VendorManager
{
    /** @var  UserManager */
    private $userManager;

    /**
     * VendorManager constructor.
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function createMain($displayName, $email, $plainPassword)
    {
        return $this->userManager->createVendor($displayName, $email, $plainPassword, UserCreatedEvent::REGISTERED_WITH_SCHOOL);
    }

    public function create(VendorData $data)
    {
        return $this->userManager->createVendor($data->name, $data->email);
    }
}