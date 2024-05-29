<?php

namespace App\Controller;

use App\Entity\Formateur;
use App\Form\FormateurType;
use App\Repository\SessionRepository;
use App\Repository\FormateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormateurController extends AbstractController
{
    #[Route('/formateur', name: 'app_formateur')]
    public function index(FormateurRepository $formateurRepository, SessionRepository $sessionRepository): Response
    {
        $formateurs = $formateurRepository->findAll();

        return $this->render('formateur/index.html.twig', [
            'formateurs' => $formateurs,
        ]);
    }


    #[Route('/formateur/form', name: 'app_formFormateur')]
    #[Route('/formateur/{id}/form_edit', name: 'app_editFormateur')]
    public function new_edit_formateur(FlashyNotifier $flashy, Formateur $formateur = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if(!$formateur){
            $formateur = new Formateur();
        }

        $formFormateur = $this->createForm(FormateurType::class, $formateur);
        $formFormateur->handleRequest($request);

        if ($formFormateur->isSubmitted() && $formFormateur->isValid()) {
            try{
                $formateur = $formFormateur->getData();
                $entityManager->persist($formateur);
                $entityManager->flush();
    
                if ($formateur->getId()) {
                    $flashy->success("Le profil du formateur $formateur a été modifé");

                    return $this->redirectToRoute("app_ficheFormateur", ['id' => $formateur->getId()]);
                } else {
                    $flashy->success("Le formateur a été ajouté");

                    return $this->redirectToRoute("app_formFormateur");
                }
    
            }
            catch (\Exception $e){

                if ($formateur->getId()) {
                    $flashy->error("Un problème est survenu lors de la modification du profil du formateur $formateur");

                    return $this->redirectToRoute("app_ficheFormateur", ['id' => $formateur->getId()]);
                } else {
                    $flashy->error("Un problème est survenu lors de l'ajout du formateur'");

                    return $this->redirectToRoute("app_formFormateur");
                }
            }

        }

        return $this->render('formateur/form.html.twig', [
            'formFormateur' => $formFormateur,
            'edit' => $formateur->getId()
        ]);
    }

    #[Route('/formateur/{id}/delete', name: 'app_deleteFormateur')]
    public function delete(FlashyNotifier $flashy, Formateur $formateur, EntityManagerInterface $entityManager){
        foreach($formateur->getSessions() as $session){
            $session->setFormateur(null);
        }

        try{
            $entityManager->remove($formateur);
            $entityManager->flush();    

            $flashy->success("Le formateur $formateur a été supprimé");
        }
        catch (\Exception $e){
            $flashy->error("Un problème est survenu lors de la suppression du formateur $formateur");
        }

        return $this->redirectToRoute("app_formateur");
    }


    #[Route('/formateur/{id}', name: 'app_ficheFormateur')]
    public function fiche(Formateur $formateur): Response
    {
        return $this->render('formateur/fiche.html.twig', [
            'formateur' => $formateur,
        ]);
    }


}
