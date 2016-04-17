<?php


namespace Fairpay\Bundle\TransactionBundle\Manager;

use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\LockMode;
use Fairpay\Bundle\TransactionBundle\Entity\Transaction;
use Fairpay\Bundle\TransactionBundle\Exception\ActiveUserIsNotStudentException;
use Fairpay\Bundle\TransactionBundle\Exception\DifferentSchoolsException;
use Fairpay\Bundle\TransactionBundle\Exception\InsufficientBalanceException;
use Fairpay\Bundle\TransactionBundle\Exception\InvalidAmountException;
use Fairpay\Bundle\TransactionBundle\Exception\IssuerAndReceiverNotDefinedException;
use Fairpay\Bundle\TransactionBundle\Exception\SameIssuerAndReceiverException;
use Fairpay\Bundle\TransactionBundle\Exception\TransactionAlreadyCanceledException;
use Fairpay\Bundle\TransactionBundle\Repository\TransactionRepository;
use Fairpay\Bundle\UserBundle\Entity\User;
use Fairpay\Bundle\UserBundle\Manager\UserManager;
use Fairpay\Util\Manager\CurrentSchoolAwareManager;


/**
 * @method TransactionRepository getRepo()
 */
class TransactionManager extends CurrentSchoolAwareManager
{
    const ENTITY_SHORTCUT_NAME = 'FairpayTransactionBundle:Transaction';
    const TYPE_DEPOSIT         = 1;
    const TYPE_WITHDRAWAL      = 2;
    const TYPE_TRANSFER        = 3;

    /** @var  UserManager */
    private $userManager;

    /**
     * TransactionManager constructor.
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @param User   $user
     * @param float  $amount
     * @param string $message
     * @throws ActiveUserIsNotStudentException
     * @throws DifferentSchoolsException
     * @throws InvalidAmountException
     * @throws ConnectionException
     */
    public function deposit(User $user, $amount, $message)
    {
        $this->execute($amount, $message, null, $user);
    }

    /**
     * @param User   $user
     * @param float  $amount
     * @param string $message
     * @throws ActiveUserIsNotStudentException
     * @throws DifferentSchoolsException
     * @throws InvalidAmountException
     * @throws InsufficientBalanceException
     * @throws ConnectionException
     */
    public function withdrawal(User $user, $amount, $message)
    {
        $this->execute($amount, $message, null, $user);
    }

    /**
     * @param float     $amount
     * @param string    $message
     * @param User|null $issuer
     * @param User|null $receiver
     * @return Transaction
     * @throws ActiveUserIsNotStudentException
     * @throws DifferentSchoolsException
     * @throws InsufficientBalanceException
     * @throws InvalidAmountException
     * @throws IssuerAndReceiverNotDefinedException
     * @throws SameIssuerAndReceiverException
     * @throws ConnectionException
     * @throws \Exception
     */
    public function create($amount, $message, User $issuer = null, User $receiver = null)
    {
        $issuedBy = $this->userManager->getActiveUser();

        if (!$issuedBy || $issuedBy->getIsVendor()) {
            throw new ActiveUserIsNotStudentException('You must be logged in as a Student to execute a transaction.');
        }

        // Check at least receiver or issuer is defined
        if (!$issuer && !$receiver) {
            throw new IssuerAndReceiverNotDefinedException('You must at least define the receiver or the issuer.');
        }

        // Check amount
        if ($amount < 0.01) {
            throw new InvalidAmountException('The amount must be greater than zero.');
        }

        $type = ($issuer ? 2 : 0) + ($receiver ? 1 : 0);

        // Check deposit
        if (self::TYPE_DEPOSIT === $type) {

            // Check same school
            if ($issuedBy->getSchool() !== $receiver->getSchool()) {
                throw new DifferentSchoolsException('The receiver is from a different school than you.');
            }

        // Check withdrawal
        } else if (self::TYPE_WITHDRAWAL === $type) {

            // Check same school
            if ($issuer->getSchool() !== $issuedBy->getSchool()) {
                throw new DifferentSchoolsException('The issuer is from a different school than you.');
            }

        // Check transfer
        } else if (self::TYPE_TRANSFER === $type) {

            // Check issuer != receiver
            if ($issuer === $receiver) {
                throw new SameIssuerAndReceiverException('The issuer and receiver can not be the same.');
            }

            // Check same school
            if ($issuer->getSchool() !== $receiver->getSchool()) {
                throw new DifferentSchoolsException('The issuer and receiver are not from the same school.');
            }

            // Check same school
            if ($issuedBy->getSchool() !== $receiver->getSchool()) {
                throw new DifferentSchoolsException('You are not from the same school than the issuer and the receiver.');
            }
        }

        $transaction = new Transaction($issuer, $receiver, $amount, $message);
        $transaction->setIssuedBy($issuedBy);
        $transaction->setSchool($this->schoolManager->getCurrentSchool());

        $this->apply($transaction);

        return $transaction;
    }

    /**
     * Update issuer and receiver balance and save transaction.
     *
     * @param Transaction $transaction
     * @throws InsufficientBalanceException
     * @throws ConnectionException
     * @throws \Exception
     */
    private function apply(Transaction $transaction)
    {
        if ($transaction->getIssuer()) {
            $this->updateBalance($transaction->getIssuer(), -$transaction->getAmount());
        }

        if ($transaction->getReceiver()) {
            $this->updateBalance($transaction->getReceiver(), $transaction->getAmount());
        }

        $this->em->persist($transaction);
        $this->em->flush();
    }

    /**
     * Update issuer and receiver balance and save transaction.
     *
     * @param Transaction $transaction
     * @throws InsufficientBalanceException
     * @throws ConnectionException
     * @throws \Exception
     */
    private function unapply(Transaction $transaction)
    {
        if ($transaction->getReceiver()) {
            $this->updateBalance($transaction->getReceiver(), -$transaction->getAmount());
        }

        if ($transaction->getIssuer()) {
            $this->updateBalance($transaction->getIssuer(), $transaction->getAmount());
        }

        $this->em->persist($transaction);
        $this->em->flush();
    }

    /**
     * Cancel a transaction and give back money to issuer.
     *
     * @param Transaction $transaction
     * @param string      $message
     * @throws ActiveUserIsNotStudentException
     * @throws TransactionAlreadyCanceledException
     * @throws InsufficientBalanceException
     * @throws ConnectionException
     * @throws \Exception
     */
    public function cancel(Transaction $transaction, $message)
    {
        $issuedBy = $this->userManager->getActiveUser();

        if (!$issuedBy || $issuedBy->getIsVendor()) {
            throw new ActiveUserIsNotStudentException('You must be logged in as a Student to execute a transaction.');
        }

        if ($transaction->getCanceled()) {
            throw new TransactionAlreadyCanceledException();
        }

        $transaction->setCanceled(true);
        $transaction->setCanceledAt(new \DateTime());
        $transaction->setCancelMessage($message);
        $transaction->setCanceledBy($issuedBy);

        $this->unapply($transaction);
    }

    /**
     * Lock the user in the DB and increase it's balance by $increment (can be negative).
     *
     * @param User  $user
     * @param float $increment
     * @param int   $retry
     * @throws InsufficientBalanceException
     * @throws ConnectionException
     * @throws \Exception
     */
    private function updateBalance(User $user, $increment, $retry = 3)
    {
        $this->em->getConnection()->beginTransaction();
        try {
            $user = $this->em->find('FairpayUserBundle:User', $user->getId(),  LockMode::PESSIMISTIC_WRITE);

            $newBalance = $user->getBalance() + $increment;

            if ($newBalance < 0) {
                throw new InsufficientBalanceException();
            }

            $user->setBalance($newBalance);

            $this->em->persist($user);
            $this->em->flush();
            $this->em->getConnection()->commit();
        } catch (InsufficientBalanceException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->em->getConnection()->rollback();

            if ($retry) {
                $this->updateBalance($user, $increment, --$retry);
            } else {
                throw $e;
            }
        }
    }

    public function getEntityShortcutName()
    {
        return self::ENTITY_SHORTCUT_NAME;
    }
}