<?php

namespace App\Controller;

use App\Entity\DateEvent;
use App\Form\DateEventFormType;
use App\Repository\DateEventRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ComponentController extends AbstractController
{
    protected $em;
    protected $eme;

    public function __construct(UserRepository $users, DateEventRepository $events)
    {
        $this->em = $users;
        $this->eme = $events;
    }

    #[Route('/app/component', name: 'component')]
    public function index(): Response
    {


        return $this->render('component_page/index.html.twig', [
            'users' => $this->em->findby(['status' => 'ACTIVE']),
            'events' => $this->eme->findEventsPagination(1, 3),
            'nbAllEvents' => $this->eme->lengthAllEvents()
        ]);
    }

    #[Route('/app/component/{id}/get-event-form', name: 'get_event_form')]
    public function getForm($id): Response
    {
        if( $id == 'new-event'){
            $event = new DateEvent;
        } else {
            $event = $this->eme
            ->find($id);

            if ( !$event ) {
                $this->addFlash('danger', 'Cet évènement est introuvable');
                return $this->redirectToRoute('component');
            }
        }

        $form = $this->createForm(DateEventFormType::class, $event);

        return $this->render('component_page/form.html.twig', [
            "event" => $event,
            "eventId" => $id,
            "form" => $form->createView(),
        ]);
    }

    #[Route('/app/component/pagination-ajax', name: 'pagination_ajax')]
    public function pagination(Request $request): Response
    {
        $params = $request->request->all();

        $events = $this->eme->findEventsPagination($params['currentPage'], $params['limit']);

        $result = [
            'data' => [],
            'pages' => 0,
            'page' => 0,
            'limit' => 0
        ];

        foreach($events['data'] as $event){
            array_push($result['data'], [
                'id' => $event->getId(),
                'name' => $event->getName(),
                'startDate' => $event->getStartDate(),
                'endDate' => $event->getEndDate(),
                'users' => []
            ]);
        }

        for($i = 0; $i < count($events['data']); $i++){
            foreach($events['data'][$i]->getUserDateEvents() as $user){
                array_push($result['data'][$i]['users'], [
                    'id' => $user->getUser()->getId(),
                    'firstName' => $user->getUser()->getFirstName(),
                    'lastName' => $user->getUser()->getLastName(),
                    'profilImage' => $user->getUser()->getProfilImage()
                ]);
            }
        }
        
        $result['pages'] = $events['pages'];
        $result['page'] = $events['page'];
        $result['limit'] = $events['limit'];

        return new JsonResponse($result);
    }

    #[Route('/app/component/{id}/update-event-ajax', name: 'update_event_ajax')]
    public function sendForm(Request $request, EntityManagerInterface $entityManagerInterface, $id): Response
    {
        if(count($request->request->all()) == 1){
            $formData = $request->request->all()['date_event_form'];
        } else {
            $formData = $request->request->all();
        }

        if( $id == 'new-event'){
            $event = new DateEvent;
        } else {
            $event = $this->eme
            ->find($id);

            if ( !$event ) {
                $this->addFlash('danger', 'Cet évènement est introuvable');
                return $this->redirectToRoute('component');
            }
        }

        if($formData && $request->getMethod() == 'POST'){
            
            if(count($request->request->all()) == 1){
                $name = $formData['name'];
                $startDate = new \DateTime($formData['startDate'], new \DateTimeZone('UTC'));
                $endDate = new \DateTime($formData['endDate'], new \DateTimeZone('UTC'));
                $localisation = $formData['localisation'];
                $description = $formData['description'];
    
                $event->setName($name)
                    ->setStartDate($startDate)
                    ->setEndDate($endDate)
                    ->setLocalisation($localisation)
                    ->setDescription($description)
                    ->setCreatedAt(new \DateTime('now'))
                    ->updatedTimestamps();
            } else {
                $startDate = new \DateTime($formData['startDate'], new \DateTimeZone('UTC'));
                $endDate = new \DateTime($formData['endDate'], new \DateTimeZone('UTC'));
    
                $event->setStartDate($startDate)
                    ->setEndDate($endDate)
                    ->setCreatedAt(new \DateTime('now'))
                    ->updatedTimestamps();
            }

            $entityManagerInterface->persist($event);
            $entityManagerInterface->flush();
        }

        return new JsonResponse($event);
    }

    #[Route('/app/component/get-events-ajax', name: 'get_events_ajax')]
    public function getAllEvent(): Response
    {
        $events = $this->eme->findby(['status' => 'ACTIVE']);

        $serializedEvents = [];

        foreach($events as $event){
            $serializedEvents[] = [
                'id' => $event->getId(),
                'title' => $event->getName() ? $event->getName() : 'Test',
                'start' => $event->getStartDate()->format('Y-m-d'),
                'end' => $event->getEndDate()->format('Y-m-d'),
                'color' => '#e2e5ed',
                'textColor' => '#405189'
            ];
        }

        return new JsonResponse($serializedEvents);
    }

    #[Route('/app/component/{id}/get-event-id', name: 'get_event_id')]
    public function getEventId($id): Response
    {
        $event = $this->eme->find($id);

        return $this->render('component_page/details.html.twig', [
            "event" => $event
        ]);
    }

    #[Route('/app/component/{id}/delete', name: 'event_delete')]
    public function delete(EntityManagerInterface $entityManagerInterface, $id)
    {
        $event = $this->eme->find($id);

        if ( !$event ) {
            $this->addFlash('danger', 'Cet évènement est introuvable');
            return $this->redirectToRoute('component');
        }

        $event->setStatus('DELETED');
        $event->updatedTimestamps();

        $entityManagerInterface->persist($event);
        $entityManagerInterface->flush();

        $this->addFlash('danger', 'Cet évènement a été supprimé');

        return $this->redirectToRoute('component');
    }
}