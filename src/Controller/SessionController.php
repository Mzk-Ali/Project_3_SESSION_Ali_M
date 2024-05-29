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
use MercurySeries\FlashyBundle\FlashyNotifier;
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
    public function addStagiaire(FlashyNotifier $flashy, int $id, int $stagiaireId, SessionRepository $sessionRepository, StagiaireRepository $stagiaireRepository, EntityManagerInterface $entityManager): Response
    {
        $session = $sessionRepository->find($id);
        $stagiaire = $stagiaireRepository->find($stagiaireId);

        try{
            $session->addStagiaire($stagiaire);
            $entityManager->persist($session);
            $entityManager->flush();

            $flashy->success("Le stagiaire $stagiaire a été ajouté à cette session");
        }
        catch (\Exception $e){
            $flashy->error("Un problème est survenu lors de l'ajout du stagiaire $stagiaire");
        }

        return $this->redirectToRoute("app_ficheSession", ['id' => $id]);
    }



    #[Route('/session/{id}/removeStagiaire/{stagiaireId}', name: 'app_removeStagiaire')]
    public function removeStagiaire(FlashyNotifier $flashy, int $id, int $stagiaireId, SessionRepository $sessionRepository, StagiaireRepository $stagiaireRepository, EntityManagerInterface $entityManager): Response
    {
        $session = $sessionRepository->find($id);
        $stagiaire = $stagiaireRepository->find($stagiaireId);

        try{
            $session->removeStagiaire($stagiaire);
            $entityManager->persist($session);
            $entityManager->flush();

            $flashy->success("Le stagiaire $stagiaire a été supprimé de cette session");
        }
        catch (\Exception $e){
            $flashy->error("Un problème est survenu lors de la suppression du stagiaire $stagiaire");
        }

        return $this->redirectToRoute("app_ficheSession", ['id' => $id]);
    }


    // #[Route('/session/{id}/addProgramme/{programmeId}', name: 'app_addProgramme')]
    // public function addProgramme(FlashyNotifier $flashy, int $id, int $programmeId, ProgrammeRepository $programmeRepository, EntityManagerInterface $entityManager): Response
    // {
    //     $programme = $programmeRepository->find($programmeId);

    //     try{
    //         $entityManager->add($programme);
    //         $entityManager->flush();

    //         $flashy->success("Le module $programme a été ajouté à cette session");
    //     }
    //     catch (\Exception $e){
    //         $flashy->error("Un problème est survenu lors de l'ajout du programme $programme");
    //     }
        
    //     return $this->redirectToRoute("app_ficheSession", ['id' => $id]);
    // }


    #[Route('/session/{id}/removeProgramme/{programmeId}', name: 'app_removeProgramme')]
    public function removeProgramme(FlashyNotifier $flashy, int $id, int $programmeId, SessionRepository $sessionRepository, ProgrammeRepository $programmeRepository, EntityManagerInterface $entityManager): Response
    {
        // $session = $sessionRepository->find($id);
        $programme = $programmeRepository->find($programmeId);

        try{
            $entityManager->remove($programme);
            $entityManager->flush();

            $flashy->success("Le module $programme a été supprimé de cette session");
        }
        catch (\Exception $e){
            $flashy->error("Un problème est survenu lors de la suppression du programme $programme");
        }

        return $this->redirectToRoute("app_ficheSession", ['id' => $id]);
    }


    #[Route('/session/form', name: 'app_formSession')]
    #[Route('/session/{id}/form_edit', name: 'app_editSession')]
    public function new_edit_session(FlashyNotifier $flashy, Session $session = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if(!$session){
            $session = new Session();
        }
        
        $formSession = $this->createForm(SessionType::class, $session);
        $formSession->handleRequest($request);

        if ($formSession->isSubmitted() && $formSession->isValid()) {
            try{
                $session = $formSession->getData();
                $entityManager->persist($session);
                $entityManager->flush();
    
                if ($session->getId()) {
                    $flashy->success("La fiche de session $session a été modifé");

                    return $this->redirectToRoute("app_ficheSession", ['id' => $session->getId()]);
                } else {
                    $flashy->success("La session a été ajouté");

                    return $this->redirectToRoute("app_formSession");
                }
    
            }
            catch (\Exception $e){

                if ($session->getId()) {
                    $flashy->error("Un problème est survenu lors de la modification de la fiche de session $session");

                    return $this->redirectToRoute("app_ficheSession", ['id' => $session->getId()]);
                } else {
                    $flashy->error("Un problème est survenu lors de l'ajout de la session");

                    return $this->redirectToRoute("app_formSession");
                }
            }
        }

        return $this->render('session/form.html.twig', [
            'formSession' => $formSession,
            'edit' => $session->getId()
        ]);
    }









    #[Route('/session/{id}/delete', name: 'app_deleteSession')]
    public function delete(Session $session, EntityManagerInterface $entityManager){

        // var_dump($session->getStagiaires());die;
        foreach($session->getProgrammes() as $programme){
            // $session->removeProgramme($programme);
            $entityManager->remove($programme);
        }
        foreach($session->getStagiaires() as $stagiaire){
            $session->removeStagiaire($stagiaire);
        }
        $entityManager->remove($session);
        $entityManager->flush();

        return $this->redirectToRoute("app_session");
    }


    #[Route('/session/{id}', name: 'app_ficheSession')]
    public function fiche(FlashyNotifier $flashy, int $id, Session $session, SessionRepository $sessionRepository, StagiaireRepository $stagiaireRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = $sessionRepository->find($id);
        $stagiaires = $stagiaireRepository->findStagiairesUnregistered($session);

        $formProgramme = $this->createForm(ProgrammeType::class);
        $formProgramme->handleRequest($request);
        
        if ($formProgramme->isSubmitted() && $formProgramme->isValid()) {
            $data = $formProgramme->getData();
            $programme = new Programme();

            $programme->setLecon($data['lecon']);
            $programme->setDuree($data['duree']);
            $programme->setSession($session);

            try{
                $entityManager->persist($programme);
                $entityManager->flush();

                $flashy->success("Le module $programme a été ajouté à cette session");
            }
            catch (\Exception $e){
                $flashy->error("Un problème est survenu lors de l'ajout du programme $programme");
            }
            return $this->redirectToRoute("app_ficheSession", ['id' => $id]);
        }

        return $this->render('session/fiche.html.twig', [
            'session' => $session,
            'stagiaires' => $stagiaires,
            'formProgramme' => $formProgramme,
        ]);
    }

}
