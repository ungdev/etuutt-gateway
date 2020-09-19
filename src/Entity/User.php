<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $casUid;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $userId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ldapUNGUid;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCasUid(): ?string
    {
        return $this->casUid;
    }

    public function setCasUid(string $casUid): self
    {
        $this->casUid = $casUid;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getLdapUNGUid(): ?string
    {
        return $this->ldapUNGUid;
    }

    public function setLdapUNGUid(?string $ldapUNGUid): self
    {
        $this->ldapUNGUid = $ldapUNGUid;

        return $this;
    }
}
