<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Programme;
use App\Entity\Stagiaire;
use App\Form\SessionType;
use App\Form\ProgrammeType;
use App\Repository\SessionRepository;
use App\Repository\ProgrammeRepository;
use App\Repository\StagiaireRepository;
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

    #[Route('/session/{id}/addStagiaire/{stagiaireId}', name: 'app_addStagiaire')]
    public function addStagiaire(int $id, int $stagiaireId, SessionRepository $sessionRepository, StagiaireRepository $stagiaireRepository, EntityManagerInterface $entityManager): Response
    {
        $session = $sessionRepository->find($id);
        $stagiaire = $stagiaireRepository->find($stagiaireId);
        $session->addStagiaire($stagiaire);

        $entityManager->persist($session);
        $entityManager->flush();

        return $this->redirectToRoute("app_ficheSession", ['id' => $id]);
    }



    #[Route('/session/{id}/removeStagiaire/{stagiaireId}', name: 'app_removeStagiaire')]
    public function removeStagiaire(int $id, int $stagiaireId, SessionRepository $sessionRepository, StagiaireRepository $stagiaireRepository, EntityManagerInterface $entityManager): Response
    {
        $session = $sessionRepository->find($id);
        $stagiaire = $stagiaireRepository->find($stagiaireId);
        $session->removeStagiaire($stagiaire);

        $entityManager->persist($session);
        $entityManager->flush();

        return $this->redirectToRoute("app_ficheSession", ['id' => $id]);
    }


    #[Route('/session/{id}/addProgramme/{programmeId}', name: 'app_addProgramme')]
    public function addProgramme(int $id, int $programmeId, ProgrammeRepository $programmeRepository, EntityManagerInterface $entityManager): Response
    {
        $programme = $programmeRepository->find($programmeId);

        $entityManager->add($programme);
        $entityManager->flush();

        return $this->redirectToRoute("app_ficheSession", ['id' => $id]);
    }


    #[Route('/session/{id}/removeProgramme/{programmeId}', name: 'app_removeProgramme')]
    public function removeProgramme(int $id, int $programmeId, SessionRepository $sessionRepository, ProgrammeRepository $programmeRepository, EntityManagerInterface $entityManager): Response
    {
        // $session = $sessionRepository->find($id);
        $programme = $programmeRepository->find($programmeId);

        $entityManager->remove($programme);
        $entityManager->flush();

        return $this->redirectToRoute("app_ficheSession", ['id' => $id]);
    }


    #[Route('/session/form', name: 'app_formSession')]
    #[Route('/session/{id}/form_edit', name: 'app_editSession')]
    public function new_edit_session(Session $session = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if(!$session){
            $session = new Session();
        }
        
        $formSession = $this->createForm(SessionType::class, $session);
        $formSession->handleRequest($request);

        if ($formSession->isSubmitted() && $formSession->isValid()) {
            $session = $formSession->getData();
            $entityManager->persist($session);
            $entityManager->flush();

            if ($session->getId()) {
                return $this->redirectToRoute("app_ficheSession", ['id' => $session->getId()]);
            } else {
                return $this->redirectToRoute("app_formSession");
            }
        }


        return $this->render('session/form.html.twig', [
            'formSession' => $formSession,
            'edit' => $session->getId()
        ]);
    }









    #[Route('/session/{id}/delete', name: 'app_deleteSession')]
    public function delete(Session $session, EntityManagerInterface $entityManager){

        var_dump($session->getStagiaires());die;
        $entityManager->remove($session);
        $entityManager->flush();

        return $this->redirectToRoute("app_session");
    }












    #[Route('/session/{id}', name: 'app_ficheSession')]
    public function fiche(int $id, Session $session, StagiaireRepository $stagiaireRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $stagiaires = $stagiaireRepository->findStagiairesUnregistered($session);

        $programme = new Programme();
        $formProgramme = $this->createForm(ProgrammeType::class, $programme);
        $formProgramme->handleRequest($request);
        
        if ($formProgramme->isSubmitted() && $formProgramme->isValid()) {
            $entityManager->persist($programme);
            $entityManager->flush();
            return $this->redirectToRoute("app_ficheSession", ['id' => $id]);
        }

        return $this->render('session/fiche.html.twig', [
            'session' => $session,
            'stagiaires' => $stagiaires,
            'formProgramme' => $formProgramme,
        ]);
    }

}
