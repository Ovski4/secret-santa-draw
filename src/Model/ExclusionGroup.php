<?php

namespace App\Model;

class ExclusionGroup
{
    /**
     * @var Participant[]
     */
    private $participants;

    public function __construct()
    {
        $this->participants = [];
    }

    public function add(Participant $participant): self
    {
        if (!in_array($participant, $this->participants)) {
            $this->participants[] = $participant;
        }

        return $this;
    }

    public function belongToGroup(Participant $participant1, Participant $participant2)
    {
        if (in_array($participant1, $this->participants) && in_array($participant2, $this->participants)) {
            return true;
        }

        return false;
    }
}
