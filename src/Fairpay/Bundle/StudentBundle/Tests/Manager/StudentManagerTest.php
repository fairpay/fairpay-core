<?php


namespace Fairpay\Bundle\StudentBundle\Tests\Manager;


use Fairpay\Bundle\SchoolBundle\Entity\School;
use Fairpay\Bundle\StudentBundle\Entity\Student;
use Fairpay\Bundle\StudentBundle\Entity\SubHistory;
use Fairpay\Bundle\StudentBundle\Form\StudentData;
use Fairpay\Bundle\StudentBundle\Manager\StudentManager;
use Fairpay\Bundle\UserBundle\Entity\User;
use Fairpay\Util\Tests\UnitTestCase;
use Prophecy\Argument;

class StudentManagerTest extends UnitTestCase
{
    const student_repository = 'Fairpay\Bundle\StudentBundle\Repository\StudentRepository';
    const token_interface    = 'Symfony\Component\Security\Core\Authentication\Token\TokenInterface';
    const sub_history        = 'Fairpay\Bundle\StudentBundle\Entity\SubHistory';

    /** @var  StudentManager */
    private $studentManager;

    // Mocked
    private $em;
    private $repo;
    private $dispatcher;
    private $tokenStorage;
    private $schoolManager;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $token = $this->mock(self::token_interface);
        $token->getUser()->willReturn(new User());

        $this->tokenStorage = $this->mock(self::security_token_storage);
        $this->tokenStorage->getToken()->willReturn($token->reveal());
        $this->studentManager = new StudentManager(
            $this->tokenStorage->reveal()
        );

        $this->em = $this->mock(self::doctrine_orm_entity_manager);
        $this->dispatcher = $this->mock(self::event_dispatcher);
        $this->studentManager->init($this->em->reveal(), $this->dispatcher->reveal());

        $this->schoolManager = $this->mock(self::school_manager);
        $this->studentManager->setSchoolManager($this->schoolManager->reveal());

        $this->repo = $this->mock(self::student_repository);
        $this->em->getRepository(StudentManager::ENTITY_SHORTCUT_NAME)->willReturn($this->repo->reveal());
    }

    public function testUpdate()
    {
        $student = new Student();
        $student->setFirstName('Bruce');
        $student->setPhone('01 22 33 44 55');
        $student->setUntouchableFields(['phone']);

        $data = new StudentData($student);
        $data->lastName = 'Wayne';
        $data->phone = null;

        $this->em->persist($student)->shouldBeCalled();
        $this->em->flush()->shouldBeCalledTimes(1);

        $this->studentManager->update($student, $data);
        $this->assertEquals('Bruce', $student->getFirstName());
        $this->assertEquals('Wayne', $student->getLastName());
        $this->assertCount(1, $student->getUntouchableFields());
        $this->assertTrue(in_array('lastName', $student->getUntouchableFields()));
    }

    public function testSubHistory()
    {
        $student = new Student();

        $data = new StudentData($student);
        $data->isSub = true;

        /** @var SubHistory $subHistory */
        $subHistory = null;
        $this->em->persist($student)->shouldBeCalled();
        $this->em->persist(Argument::type(self::sub_history))->will(function($args) use(&$subHistory) {
            $subHistory = $args[0];
        });
        $this->em->flush()->shouldBeCalledTimes(2);

        $this->studentManager->update($student, $data);
        $this->assertEquals($student, $subHistory->getStudent());
        $this->assertEquals(true, $subHistory->getState());
    }

    public function testFindOneById()
    {
        $id = 42;
        $school = new School();

        $this->repo->findOneBy(array(
            'school' => $school,
            'id' => $id
        ))->shouldBeCalled();

        $this->schoolManager->getCurrentSchool()->willReturn($school);

        $this->studentManager->findStudentById($id);
    }
}