<?php

namespace App\Controller;

use App\Form\EditProfilFormType;
use App\Form\ProfilChangePasswordFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class ProfilController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public object $user;
    protected $em;

    public function __construct(UserRepository $userRepository, 
    private ResetPasswordHelperInterface $resetPasswordHelper,
    private EntityManagerInterface $entityManager)
    {
        $this->em = $userRepository;
    }

    #[Route('/app/profil', name: 'profil')]
    public function index(): Response
    {
        $this->user = $this->getUser();

        return $this->render('profil/index.html.twig', [
            'user' => $this->user,
        ]);
    }

    #[Route('/app/profil/edit/', name: 'profil_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManagerInterface, UserPasswordHasherInterface $passwordHasher): Response
    {
        $userCurrent = $this->getUser();
        $userUpdate = $this->em->find($userCurrent->getId());

        $formChangePassword = $this->createForm(ProfilChangePasswordFormType::class);
        $formEditProfil = $this->createForm(EditProfilFormType::class, $userUpdate, Array("validation_groups" => "update"));
        
        $formEditProfilValid = null;
        $formEditProfil->handleRequest($request);

        if($formEditProfil->isSubmitted() && $formEditProfil->isValid()){
            $formEditProfilValid = true;

            if($formEditProfilValid){
                $now = new \DateTime();
                //ajouter date UTC0 dans le setter
                $userUpdate->setUpdatedAt(new \DateTime());

                $entityManagerInterface->persist($userUpdate);
                $entityManagerInterface->flush();

                $this->addFlash('success', "L'utilisateur a été modifié");

                return $this->redirectToRoute('profil_edit');
            }
        }

        $formChangePassword->handleRequest($request);

        if($formChangePassword->isSubmitted() && $formChangePassword->isValid()){

            $oldPassword = $formChangePassword->get('oldPassword')->getData();
            
            if($passwordHasher->isPasswordValid($userCurrent, $oldPassword)){
                $newPassword = $formChangePassword->get('plainPassword')->get('first')->getData();
                
                if($oldPassword === $newPassword){
                    $this->addFlash('danger', "Le nouveau mot de passe ne peut pas être le même que l'ancien mot de passe");
                    return $this->redirectToRoute('profil_edit', ['mdp']);

                } else {
                    $encodedPassword = $passwordHasher->hashPassword(
                        $userUpdate,
                        $formChangePassword->get('plainPassword')->getData()
                    );
        
                    $userUpdate->setPassword($encodedPassword);
                    $userUpdate->updatedTimestamps();
        
                    $entityManagerInterface->persist($userUpdate);
                    $entityManagerInterface->flush();
        
                    $this->addFlash('success', 'Votre nouveau mot de passe a bien été enregistré');
                }

            } else {
                $this->addFlash('danger', 'Votre mot de passe actuel est incorrect');
                return $this->redirectToRoute('profil_edit', ['mdp']);
            }

            return $this->redirectToRoute('profil_edit', ['mdp']);
        }

        return $this->render('profil/update.html.twig', [
            'user' => $userUpdate,
            'formEditProfil' => $formEditProfil->createView(),
            'formPassword' => $formChangePassword->createView()
        ]);
    }

    #[Route('/app/profil/image-upload-ajax', name: 'profil_image_upload_ajax')]
    public function imageUpload(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $user = $this->getUser();

        $dirUpload = $this->getParameter('img_upload_directory');
        $dirPersist = $this->getParameter('img_url');

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
                $file_name = 'profil_upload_' . uniqid() . '.' . $file_type;

                file_put_contents($dirUpload . $file_name, base64_decode($data[1]));

                $user->setProfilImage($dirPersist . $file_name);
                $user->updatedTimestamps();

                $entityManagerInterface->persist($user);
                $entityManagerInterface->flush();
            }
        }

        return new JsonResponse($user);
    }
}
