<?php


namespace Fairpay\Bundle\StudentBundle\Request\ParamConverter;


use Fairpay\Bundle\StudentBundle\Entity\Student;
use Fairpay\Bundle\StudentBundle\Manager\StudentManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StudentParamConverter implements ParamConverterInterface
{
    /** @var  StudentManager */
    private $studentManager;

    /**
     * StudentParamConverter constructor.
     * @param StudentManager $studentManager
     */
    public function __construct(StudentManager $studentManager)
    {
        $this->studentManager = $studentManager;
    }

    /**
     * Stores the object in the request.
     *
     * @param Request        $request       The request
     * @param ParamConverter $configuration Contains the name, class and options of the object
     *
     * @return bool True if the object has been successfully set, else false
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $student = $this->studentManager->findStudentById($request->attributes->get('id'));

        if (null === $student) {
            throw new NotFoundHttpException('Cet étudiant n\'existe pas.');
        }

        $request->attributes->set($configuration->getName(), $student);
        return true;
    }

    /**
     * Checks if the object is supported.
     *
     * @param ParamConverter $configuration Should be an instance of ParamConverter
     *
     * @return bool True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() === Student::class;
    }
}