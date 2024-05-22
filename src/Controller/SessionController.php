<?php

namespace App\Controller;

use App\Entity\Session;
use App\Form\SessionType;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    public function index(SessionRepository $sessionRepository): Response
    {
        $sessions = $sessionRepository->findAll();

        return $this->render('session/index.html.twig', [
            'sessions' => $sessions,
        ]);
    }


    #[Route('/session/{$id}', name: 'app_ficheSession')]
    public function fiche(Session $session): Response
    {
        return $this->render('session/fiche.html.twig', [
            'session' => $session,
        ]);
    }

    #[Route('/session/form', name: 'app_formSession')]
    public function formSession(Request $request): Response
    {
        $session = new Session();
        $formSession = $this->createForm(SessionType::class, $session);
        $formSession->handleRequest($request);


        return $this->render('session/form.html.twig', [
            'formSession' => $formSession,
        ]);
    }
}
