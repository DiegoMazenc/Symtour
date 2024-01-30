<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\User;
use Aws\S3\S3Client;
use App\Entity\Profil;
use App\Entity\ChatRoom;
use App\Form\ProfilType;
use App\Entity\BandMember;
use App\Entity\HallMember;
use App\Entity\Notification;
use Doctrine\ORM\Mapping\Id;
use App\Security\EmailVerifier;
use Aws\Credentials\Credentials;
use Aws\S3\Exception\S3Exception;
use App\Repository\BandRepository;
use App\Repository\ChatRepository;
use Symfony\Component\Mime\Address;
use App\Repository\ProfilRepository;
use App\Service\NotificationService;
use App\Repository\ChatRoomRepository;
use App\Repository\BandMemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NotificationRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
        $notification->isRead((int)$request->query->get('notification_id'));

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

    #[Route('/{id}/notification', name: 'app_profil_notification', methods: ['GET'])]
    public function notification(Profil $profil, ProfilRepository $profilRepository, BandMemberRepository $bandMemberRepository): Response
    {

        return $this->render('profil/notification.html.twig', [
            'profil' => $profil,

        ]);
    }

    #[Route('/{id}/chat-room', name: 'app_profil_chat-room', methods: ['GET'])]
    public function chatRoom(Profil $profil,ChatRoomRepository $chatRoomRepository): Response
    {
        $chatBand = $chatRoomRepository->chatRoomByProfilBand($profil);

        $chatHall = $chatRoomRepository->chatRoomByProfilHall($profil);
        // dd($chatHall, $chatBand);

        return $this->render('profil/chatroom.html.twig', [
            'chatHall' => $chatHall,
            'chatBand' => $chatBand,

        ]);
    }

    // #[Route('/{id}/chat-room/{chatRoom}/chat', name: 'app_profil_chat', methods: ['GET', 'POST'])]
    // public function chat(Profil $profil,Chat $chat, ChatRoom $chatRoom, ChatRoomRepository $chatRoomRepository, ChatRepository $chatRepository): Response
    // {
    //     $chat = $chatRepository->addChat($chatRoom);
    //     dd($chat);

    //     return $this->render('profil/chat.html.twig', [
    //         'chat' => $chat,

    //     ]);
    // }


    #[Route('/{id}/edit', name: 'app_profil_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Profil $profil, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProfilType::class, $profil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        $img = $form->get('picture')->getData();
        if ($img){
            $originalName = pathinfo($img->getClientOriginalName(), PATHINFO_FILENAME);
            $nameSlug = $slugger->slug($originalName);
            $fileName = $nameSlug . '-' . uniqid().'.'.$img->guessExtension();
            try{
                $sha256 = hash_file('sha256', $img->getRealPath());
                $credentials = new Credentials('AKIAZQ3DNS574T63MPNV','2yTo3/9lJ7YFOCb5gAbQ7yGE2m08iy5XyyKy6Ur4');

                $s3 = new S3Client([
                    'version' => 'latest',
                    'region'  => 'eu-west-3',
                    'credentials'  => $credentials
                ]);

                $s3->putObject([
                    'Bucket' => 'symtour',
                    'Key'    => $fileName,
                    'Body'   => $img,
                    'SourceFile' => $img->getRealPath(),
                    'ContentType' => $img->getMimeType(),
                    'ContentSHA256' => $sha256,
                    'ACL'    => 'public-read',
                ]);
                // dd($s3->getObjectUrl('symtour', $fileName));
            } catch (S3Exception $e){
                dd($e->getMessage());

            }
            $profil->setPicture($s3->getObjectUrl('symtour', $fileName));

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
        }

        return $this->redirectToRoute('app_profil_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/resend-mail-confirmation', name: 'app_resend_confirmation_mail', methods: ['GET'])]
    public function resendMailConfirmation($id, EmailVerifier $emailVerifier): Response
    {
        $emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $this->getUser(),
            (new TemplatedEmail())
                ->from(new Address('diego.mazenc@gmail.com', 'Symtour'))
                ->to($this->getUser()->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
        $this->addFlash('warning', 'Email a bien été envoyé!');
        return $this->redirectToRoute('app_profil_show', ["id" => (int)$id], Response::HTTP_SEE_OTHER);
    }
}
