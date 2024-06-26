<?php

namespace App\Controller;

use App\Service\AddPhotosService;
use App\Entity\Profil;
use App\Form\ProfilType;
use App\Entity\BandMember;
use App\Entity\HallMember;
use App\Security\EmailVerifier;
use App\Repository\BandRepository;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\ProfilRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NotificationRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

#[Route('/profil')]
class ProfilController extends AbstractController
{
    #[Route('/', name: 'app_profil_index', methods: ['GET'])]
    public function index(ProfilRepository $profilRepository): Response
    {
        return $this->render('profil/index.html.twig', [
            'profils' => $profilRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_profil_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $profil = new Profil();
        $form = $this->createForm(ProfilType::class, $profil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($profil);
            $entityManager->flush();

            return $this->redirectToRoute('app_profil_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profil/new.html.twig', [
            'profil' => $profil,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_profil_show', methods: ['GET', 'POST'])]
    public function show(
        Request $request,
        Profil $profil,
        NotificationService $notification,
        EntityManagerInterface $em,
        BandRepository $bandRepository
    ): Response {
        $notification->isRead((int) $request->query->get('notification_id'));

        if ($request->isMethod('POST')) {
            $action = $request->request->get('action');

            if ($action === 'validate') {
                $status = "member";
            } elseif ($action === 'reject') {
                $status = "reject";
            } else {
                $status = "guest";
            }
            $page = $request->request->get('page');
            if ($page == "band") {
                $bandMemberId = $request->request->get('bandMemberId');
                $bandMember = $em->getRepository(BandMember::class)->find($bandMemberId);
                if ($bandMember) {
                    $bandMember->setStatus($status);
                    $em->flush();

                    $bandId = $bandMember->getBand()->getId();
                    $band = $bandMember->getBand();
                    $profilPseudo = $profil->getPseudo();
                    $profilId = $profil->getId();
                    $notification->addNotificationProfilToBand("band", $profilPseudo, $bandId, "profil", $profilId, "add", $band, $status, $em);
                }
            }
            if ($page == "hall") {
                $hallMemberId = $request->request->get('hallMemberId');
                $hallMember = $em->getRepository(HallMember::class)->find($hallMemberId);
                if ($hallMember) {
                    $hallMember->setStatus($status);
                    $em->flush();

                    $hallId = $hallMember->getHall()->getId();
                    $hall = $hallMember->getHall();
                    $profilPseudo = $profil->getPseudo();
                    $profilId = $profil->getId();
                    $notification->addNotificationProfilToHall("hall", $profilPseudo, $hallId, "profil", $profilId, "add", $hall, $status, $em);
                }
            }
        }
        return $this->render('profil/show.html.twig', [
            'profil' => $profil,


        ]);
    }

    #[Route('/{id}/notification', name: 'app_profil_notification', methods: ['GET', 'POST'])]
    public function notification(Profil $profil, EntityManagerInterface $em, Request $request, NotificationRepository $notificationRepository, TokenInterface $token): Response
    {
        if (!$this->isGranted('profil_edit', $profil)) {
            $user = $token->getUser();
            return $this->redirectToRoute('app_profil_notification', ["id" => $user->getProfil()->getId()], Response::HTTP_SEE_OTHER);

        }
        ;
        if ($request->isMethod('POST')) {
            $idNotif = $request->request->get('id_notif');
            $notification = $notificationRepository->find((int) $idNotif);
            $em->remove($notification);
            $em->flush();
        }

        return $this->render('profil/notification.html.twig', [
            'profil' => $profil,

        ]);
    }

    // #[Route('/{id}/chat-room', name: 'app_profil_chat-room', methods: ['GET'])]
    // public function chatRoom(Profil $profil,ChatRoomRepository $chatRoomRepository): Response
    // {
    //     $chatBand = $chatRoomRepository->chatRoomByProfilBand($profil);

    //     $chatHall = $chatRoomRepository->chatRoomByProfilHall($profil);
    //     // dd($chatHall, $chatBand);

    //     return $this->render('profil/chatroom.html.twig', [
    //         'chatHall' => $chatHall,
    //         'chatBand' => $chatBand,

    //     ]);
    // }

    // #[Route('/{id}/chat-room/{chatRoom}/chat', name: 'app_profil_chat', methods: ['GET', 'POST'])]
    // public function chat(Profil $profil,Chat $chat, ChatRoom $chatRoom, ChatRoomRepository $chatRoomRepository, ChatRepository $chatRepository): Response
    // {
    //     $chat = $chatRepository->addChat($chatRoom);
    //     dd($chat);

    //     return $this->render('profil/chat.html.twig', [
    //         'chat' => $chat,

    //     ]);
    // }

    /**
     *
     * @IsGranted("ROLE_ADMIN")
     * @IsGranted("POST_SHOW")
     */
    #[Route('/{id}/edit', name: 'app_profil_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Profil $profil, TokenInterface $token, EntityManagerInterface $entityManager, AddPhotosService $addPhotosService): Response
    {
        if (!$this->isGranted('profil_edit', $profil)) {
            $user = $token->getUser();
            return $this->redirectToRoute('app_profil_edit', ["id" => $user->getProfil()->getId()], Response::HTTP_SEE_OTHER);
        }
        ;

        $form = $this->createForm(ProfilType::class, $profil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $img = $form->get('picture')->getData();
            if ($img) {
                $addPhotosService->addNewPicture($img, $profil);
            }
            $entityManager->flush();
            return $this->redirectToRoute('app_profil_show', ["id" => $profil->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profil/edit.html.twig', [
            'profil' => $profil,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_profil_delete', methods: ['POST'])]
    public function delete(Request $request, Profil $profil, EntityManagerInterface $entityManager): Response
    {

        if ($this->isCsrfTokenValid('delete' . $profil->getId(), $request->request->get('_token'))) {
            $entityManager->remove($profil);
            $entityManager->flush();
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('app_profil_edit', ["id" => $profil->getId()], Response::HTTP_SEE_OTHER);


    }

    #[Route('/{id}/resend-mail-confirmation', name: 'app_resend_confirmation_mail', methods: ['GET'])]
    public function resendMailConfirmation($id, EmailVerifier $emailVerifier): Response
    {
        $mailUser = $this->getUser()->getEmail();
        $emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $this->getUser(),
            (new TemplatedEmail())
                ->from(new Address('diego.mazenc@gmail.com', 'Symtour'))
                ->to($this->getUser()->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
        $this->addFlash('warning', 'Un nouveau mail de confirmation a été envoyé à '. $mailUser .' !');
        return $this->redirectToRoute('app_profil_show', ["id" => (int) $id], Response::HTTP_SEE_OTHER);
    }
}
