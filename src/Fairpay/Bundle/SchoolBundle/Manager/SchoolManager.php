<?php

namespace Fairpay\Bundle\SchoolBundle\Manager;


use Fairpay\Bundle\SchoolBundle\Form\Entity\SchoolRegister;
use Fairpay\Util\Manager\EntityManager;

class SchoolManager extends EntityManager
{
    const ENTITY_SHORTCUT_NAME = 'FairpaySchoolBundle:School';

    public function __construct(\Doctrine\ORM\EntityManager $entityManager)
    {
        parent::__construct($entityManager);
    }

    public function register(SchoolRegister $schoolRegister)
    {
    }

    public function getEntityShortcutName()
    {
        return self::ENTITY_SHORTCUT_NAME;
    }
}