<?php

namespace Fairpay\Bundle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Token
 *
 * @ORM\Table(name="token")
 * @ORM\Entity(repositoryClass="Fairpay\Bundle\UserBundle\Repository\TokenRepository")
 */
class Token
{
    const RESET_PASSWORD = 0;
    const REGISTER = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255, unique=true)
     */
    private $token;

    /**
     * @var array
     *
     * @ORM\Column(name="payload", type="json_array", nullable=true)
     */
    private $payload;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Fairpay\Bundle\UserBundle\Entity\User", fetch="EAGER")
     */
    private $user;

    /**
     * Token constructor.
     * @param User    $user
     * @param integer $type
     * @param string  $token
     * @param array   $payload
     */
    public function __construct(User $user, $type, $token, array $payload = null)
    {
        $this->type    = $type;
        $this->token   = $token;
        $this->payload = $payload;
        $this->user    = $user;
    }

    public function __toString()
    {
        return $this->token;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Token
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return Token
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set payload
     *
     * @param array $payload
     *
     * @return Token
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * Get payload
     *
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }
}

