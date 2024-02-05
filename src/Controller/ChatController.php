<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\ChatImage;
use App\Entity\ChatMessage;
use App\Entity\UserChat;
use App\Repository\ChatMessageRepository;
use App\Repository\ChatRepository;
use App\Repository\UserChatRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ChatController extends AbstractController
{
    private $chatRepository;
    private $userChatRepository;
    private $chatMessageRepository;
    private $userRepository;

    public function __construct(
        ChatRepository $chatRepository,
        UserChatRepository $userChatRepository,
        ChatMessageRepository $chatMessageRepository,
        UserRepository $userRepository
        ){
        $this->chatRepository = $chatRepository;
        $this->userChatRepository = $userChatRepository;
        $this->chatMessageRepository = $chatMessageRepository;
        $this->userRepository = $userRepository;
    }

    #[Route('/app/chats', name: 'chats')]
    public function index(): Response
    {
        $currentUser = $this->getUser();
        $filteredChats = $this->chatRepository->filterChatByCurrentUser($currentUser->getId());
        $chatMessages = $this->chatMessageRepository->findAll();
        $listChatUsers = $this->userRepository->findAllChatUser();


        return $this->render('chats/index.html.twig', [
            "chats" => $filteredChats,
            "currentUserId" => $currentUser->getId(),
            "allChatUsers" => $listChatUsers
        ]);
    } 

    #[Route('/app/chats/{id}/get-chat-message-from-speaker-ajax', name: 'get_chat_message_from_speaker_ajax')]
    public function getChatMessageFromSpeakerAjax(int $id): Response
    {
        $currentUserId = $this->getUser();
        $speakerChatMessage = $this->chatRepository->loadChatMessageFromSpeakerAjax($id, $currentUserId->getId());

        return new JsonResponse($speakerChatMessage);
    }

    #[Route('/app/chats/send-chat-message-ajax', name: 'send_chat_message_ajax')]
    public function sendChatMessageAjax(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $currentUser = $this->getUser();
        $formData = $request->request->all();
        $data = [];

        $chatId = $this->chatRepository->findChatId($formData['senderId'], $currentUser->getId());

        $chatMessage = new ChatMessage();
        $chatMessage->setContent($formData['content'])
        ->setUserSenderId($currentUser)
        ->setUserReceiverId($this->userRepository->find(['id' => $formData['senderId']]))
        ->setChatId($this->chatRepository->find(['id' => $chatId]))
        ->setHasDropdown(1)
        ->setStatus('ACTIVE')
        ->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('UTC')))
        ->updatedTimestamps();

        $entityManagerInterface->persist($chatMessage);
        $entityManagerInterface->flush();

        $data['id'] = $chatMessage->getId();
        $data['content'] =  $formData['content'];
        $data['firstNameSender'] =  $currentUser->getFirstName();
        $data['lastNameSender'] =  $currentUser->getLastName();
        $data['date'] = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        return new JsonResponse($data);
    }


    #[Route('/app/chats/{id}/delete-chat-message-ajax', name: 'delete_chat_message_ajax')]
    public function deleteChatMessage(EntityManagerInterface $entityManagerInterface, int $id): Response
    {
        $chatMessage = $this->chatMessageRepository->find(['id' => $id]);
        $res = [];

        try {
            $chatMessage->setStatus('DELETED')
            ->updatedTimestamps();

            $entityManagerInterface->persist($chatMessage);
            $entityManagerInterface->flush();

            $res['status'] = 200;
            $res['content'] = 'Chat Message has been successfully deleted';
        } catch (\Throwable $th) {
            $res['status'] = 404;
            $res['content'] = $th;
        }

        return new JsonResponse($res);
    }

    #[Route('/app/chats/{id}/create-chat-ajax', name: 'create_chat_ajax')]
    public function createChatAjax(EntityManagerInterface $entityManagerInterface, $id): Response
    {
        $currentUser = $this->getUser();

        $isChatUserExist = $this->chatRepository->isChatUserExist($id, $currentUser->getId()); 

        if($isChatUserExist){
            $res['status'] = 409;
            $res['content'] = "Chat User already exists";
            $res['user'] = $this->userRepository->findForChat($isChatUserExist);
            return new JsonResponse($res);
        }

        $userChat = new UserChat();
        $userChat->setUserReceiver($this->userRepository->find(['id' => $id]))
        ->setUserSender($this->userRepository->find(['id' => $currentUser->getId()]))
        ->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('UTC')))
        ->updatedTimestamps();

        $entityManagerInterface->persist($userChat);

        $chat = new Chat();
        $chat->setStatus('ACTIVE')
        ->setUserChatId($userChat)
        ->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('UTC')))
        ->updatedTimestamps();

        $entityManagerInterface->persist($chat);
        $entityManagerInterface->flush();
        
        $contactId = $userChat->getUserReceiver()->getId() == $currentUser->getId() ? $userChat->getUserSender()->getId() : $userChat->getUserReceiver()->getId();
        $res['status'] = 200;
        $res['content'] = "UserChat and Chat have been successfully added";
        $res['user'] = $this->userRepository->findForChat($contactId);

        return new JsonResponse($res);
    }

    #[Route('/app/chats/image-send-ajax', name: 'chats_image_send_ajax')]
    public function imageUpload(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $currentUser = $this->getUser();
        $formData = $request->request->all();
        $chatImage = new ChatImage();
        $data = [];

        $chatId = $this->chatRepository->findChatId($formData['senderId'], $currentUser->getId());
        
        $dirUpload = $this->getParameter('chats_img_upload_directory');
        $dirPersist = $this->getParameter('chats_img_url');
        $chatDir = 'chat-'. min($formData['senderId'], $currentUser->getId()) . '-' . max($formData['senderId'], $currentUser->getId()) . '/';
        
        $filesystem = new Filesystem();

        if(!$filesystem->exists($dirUpload . $chatDir)){
            $filesystem->mkdir($dirUpload . $chatDir, 0755);
        }

        if($request->getMethod() == 'POST'){
            $file_type = "";
            $image = $request->request->get('file');
            $data = explode(';base64,', $image);

            if($data[0] == 'data:image/jpeg' || $data[0]  == 'data:image/jpg')
                $file_type = 'jpeg';
            else if($data[0]  == 'data:image/png')
                $file_type = 'png';
            else 
                $file_type = 'other';

            if(in_array($file_type, ['jpeg', 'png'])){
                $file_name = 'img_upload_' . uniqid() . '.' . $file_type;

                file_put_contents($dirUpload . $chatDir . $file_name, base64_decode($data[1]));

                $chatImage->setImage($dirPersist . $chatDir . $file_name)
                ->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('UTC')))
                ->updatedTimestamps();

                $entityManagerInterface->persist($chatImage);
            }
        }

        $chatMessage = new ChatMessage();
        $chatMessage->setContent('')
        ->setUserSenderId($currentUser)
        ->setUserReceiverId($this->userRepository->find(['id' => $formData['senderId']]))
        ->setChatId($this->chatRepository->find(['id' => $chatId]))
        ->setHasDropdown(1)
        ->setStatus('ACTIVE')
        ->setImage($chatImage)
        ->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('UTC')))
        ->updatedTimestamps();

        $entityManagerInterface->persist($chatMessage);
        $entityManagerInterface->flush();

        $data['id'] = $chatMessage->getId();
        $data['image'] = $chatImage->getImage();
        $data['firstNameSender'] =  $currentUser->getFirstName();
        $data['lastNameSender'] =  $currentUser->getLastName();
        $data['date'] = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        return new JsonResponse($data);
    }
}