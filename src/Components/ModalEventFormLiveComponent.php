<?php

namespace App\Components;

use App\Entity\DateEvent;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;

#[AsLiveComponent]
class ModalEventFormLiveComponent extends AbstractController
{
    use DefaultActionTrait;
    use ValidatableComponentTrait;

    #[LiveProp(writable: true)]
    public string $name;

    #[LiveProp(writable: true)]
    public DateTime $date;

    #[LiveProp(writable: true)]
    public string $startsAt;

    #[LiveProp(writable: true)]
    public string $endsAt;

    #[LiveProp(writable: true)]
    public string $localisation;

    #[LiveProp(writable: true)]
    public string $description;

    #[LiveAction]
    public function save(EntityManagerInterface $entityManager): Response
    {
        dd('Enter');
        $this->validate();
        $isValid = false;
        $isSubmit = false;

        $event = new DateEvent();
        $event->setName($this->name);
        // $event->setDateScheduled($this->date);
        // $event->setStartsAt($this->startsAt);
        // $event->setEndsAt($this->endsAt);
        $event->setLocalisation($this->localisation);
        $event->setDescription($this->description);

        $isSubmit = true;

        if($isSubmit){
            $entityManager->persist($event);
            $isValid = true;

            if($isValid){
                $entityManager->flush();
                $this->addFlash('success', 'Un évènement a bien été créé');
                dd("Valid");
            } else {
                dd("Error");
            }
        }

        return $this->redirectToRoute('component');
    }
}