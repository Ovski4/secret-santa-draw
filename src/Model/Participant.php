<?php

namespace App\Model;

class Participant
{
    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $email;

    public function __construct(string $firstName, string $lastName, string $email)
    {
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function __toString()
    {
        return $this->getFullName();
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFullName() : string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
