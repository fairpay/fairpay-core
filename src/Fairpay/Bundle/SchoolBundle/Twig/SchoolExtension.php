<?php


namespace Fairpay\Bundle\SchoolBundle\Twig;


use Fairpay\Bundle\SchoolBundle\Manager\SchoolManager;

class SchoolExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    /** @var  SchoolManager */
    private $schoolManager;

    /**
     * SchoolExtension constructor.
     * @param SchoolManager $schoolManager
     */
    public function __construct(SchoolManager $schoolManager)
    {
        $this->schoolManager = $schoolManager;
    }

    public function getGlobals()
    {
        return array(
            'school' => $this->schoolManager->getCurrentSchool(),
        );
    }

    public function getName()
    {
        return 'school_extension';
    }
}