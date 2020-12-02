<?php

namespace App\Command;

use App\Model\Association;
use App\Model\ExclusionGroup;
use App\Model\Participant;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class DrawCommand extends Command
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var array
     */
    private $config;

    protected static $defaultName = 'app:draw-secret-santa';

    public function __construct(MailerInterface $mailer)
    {
        parent::__construct();

        $antoine = new Participant('Antoine', 'Salamanco', 'salamanco.antoine@yopmail.com');
        $lena = new Participant('Lena', 'Delamarre', 'lena.delamarre@yopmail.com');
        $baptiste = new Participant('Baptiste', 'Debonaire', 'baptiste.debonaire@yopmail.com');
        $loraine = new Participant('Loraine', 'Mela', 'melaloraine@yopmail.com');
        $clemence = new Participant('Clémence', 'Bidigue', 'clemence.bidigue@yopmail.com');
        $lise = new Participant('Lise', 'Frater', 'lise.frater@yopmail.com');
        $bruno = new Participant('Bruno', 'Meliore', 'brunomeliore@yopmail.com');

        $antoineCouple = new ExclusionGroup();
        $antoineCouple->add($antoine)->add($lena);

        $liseCouple = new ExclusionGroup();
        $liseCouple->add($lise)->add($bruno);

        $baptisteCouple = new ExclusionGroup();
        $baptisteCouple->add($baptiste)->add($loraine);

        $this->config = [
            'participants' => [$antoine, $lena, $baptiste, $loraine, $clemence, $lise, $bruno],
            'exclusion_groups' => [$antoineCouple, $liseCouple, $baptisteCouple]
        ];

        $this->mailer = $mailer;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $associations = $this->createAssociations();
        while ($this->containsMatchingAssociations($associations)) {
            $associations = $this->createAssociations();
        }

        /** @var $association Association */
        foreach ($associations as $association) {
            $email = (new Email())
                ->from('baptiste.bouchereau@gmail.com')
                ->to($association->getParticipantGiver()->getEmail())
                ->subject('Noël 2020 - Tu fais un cadeau à ' . $association->getParticipantReceiver()->getFirstName())
                ->html(sprintf(
                    '<p>Salut %s,</p>
                    <p>Tu es tombé sur %s !</p>
                    <p>À bientôt !</p>
                    <br><br><br><br>
                    <i style="font-size: 12px">Envoyé depuis mon sèche-linge</i>
                    ',
                    $association->getParticipantGiver()->getFirstName(),
                    $association->getParticipantReceiver()->getFirstName()
                ))
            ;

            $output->writeln('Mail send to '. $association->getParticipantGiver()->getEmail());
            $this->mailer->send($email);
        }

        return Command::SUCCESS;
    }

    private function createAssociations() : array
    {
        $associations = [];

        $participants = $this->config['participants'];

        $counter = 1;

        while (count($participants) > 0) {

            $counter++;

            shuffle($participants);

            $participant1 = $participants[0];
            $participant2 = $participants[1];

            $association = new Association($participant1, $participant2);

            if (
                !$association->matchesExclusionGroup($this->config['exclusion_groups']) &&
                !$association->hasParticipantsThatGiveOrReceiveAlready($associations)// &&
            ) {
                $associations[] = $association;
            };

            if ($this->participantGivesAndReceive($participant1, $associations)) {
                $key = array_search($participant1, $participants);
                unset($participants[$key]);
            }

            if ($this->participantGivesAndReceive($participant2, $associations)) {
                $key = array_search($participant2, $participants);
                unset($participants[$key]);
            }

            // reset the participants when someone end up alone
            if (count($participants) === 1) {
                $associations = [];
                $participants = $this->config['participants'];
            }

            // reset the participants when excluded participants end up together
            if (count($participants) === 2 && $association->matchesExclusionGroup($this->config['exclusion_groups'])) {
                $associations = [];
                $participants = $this->config['participants'];
            }
        }

        return $associations;
    }

    private function participantGivesAndReceive(Participant $participant, $associations)
    {
        $isGiver = false;
        $isReceiver = false;

        /** @var $association Association */
        foreach ($associations as $association) {
            if ($association->getParticipantGiver() === $participant) {
                $isGiver = true;
            }

            if ($association->getParticipantReceiver() === $participant) {
                $isReceiver = true;
            }
        }

        return $isGiver && $isReceiver;
    }

    private function containsMatchingAssociations(array $associations)
    {
        /** @var $association Association */
        foreach ($associations as $association) {

            /** @var $association2 Association */
            foreach ($associations as $association2) {
                if ($association === $association2) {
                    continue;
                }

                if (
                    $association->getParticipantGiver() === $association2->getParticipantReceiver() &&
                    $association->getParticipantReceiver() === $association2->getParticipantGiver()) {
                    return true;
                }
            }
        }

        return false;
    }
}
