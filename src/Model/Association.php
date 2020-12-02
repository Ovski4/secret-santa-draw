<?php

namespace App\Model;

class Association
{
    /**
     * @var Participant
     */
    private $participantGiver;

    /**
     * @var Participant
     */
    private $participantReceiver;

    public function __construct(Participant $participantGiver, Participant $participantReceiver)
    {
        $this->participantGiver = $participantGiver;
        $this->participantReceiver = $participantReceiver;
    }

    public function __toString()
    {
        return $this->participantGiver->getFullName() . ' => ' . $this->participantReceiver->getFullName();
    }

    public function getParticipantGiver(): Participant
    {
        return $this->participantGiver;
    }

    public function getParticipantReceiver(): Participant
    {
        return $this->participantReceiver;
    }

    public function matchesExclusionGroup($exclusionGroups)
    {
        /** @var $exclusionGroup ExclusionGroup */
        foreach ($exclusionGroups as $exclusionGroup) {
            if ($exclusionGroup->belongToGroup($this->participantGiver, $this->participantReceiver)) {
                return true;
            }
        }

        return false;
    }

    public function hasParticipantsThatGiveOrReceiveAlready($existingAssociations)
    {
        /** @var $association Association */
        foreach ($existingAssociations as $association) {
            if (
                $this->participantGiver === $association->participantGiver ||
                $this->participantReceiver === $association->participantReceiver
            ) {
                return true;
            }
        }

        return false;
    }

    public function participantsAlreadyMatched($existingAssociations)
    {
        /** @var $association Association */
        foreach ($existingAssociations as $association) {
            if (
                ($this->participantGiver === $association->participantReceiver &&
                $this->participantReceiver === $association->participantGiver) ||
                ($this->participantGiver === $association->participantGiver &&
                $this->participantReceiver === $association->participantReceiver)
            ) {
                return true;
            }
        }

        return false;
    }
}
