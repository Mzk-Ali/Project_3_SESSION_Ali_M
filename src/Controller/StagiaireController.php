<?php

namespace App\Controller;

use App\Entity\Stagiaire;
use App\Form\StagiaireType;
use App\Repository\StagiaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StagiaireController extends AbstractController
{
    #[Route('/stagiaire', name: 'app_stagiaire')]
    public function index(StagiaireRepository $stagiaireRepository): Response
    {
        $stagiaires = $stagiaireRepository->findAll();

        return $this->render('stagiaire/index.html.twig', [
            'stagiaires' => $stagiaires,
        ]);
    }

    #[Route('/stagiaire/form', name: 'app_formStagiaire')]
    #[Route('/stagiaire/{id}/form_edit', name: 'app_editStagiaire')]
    public function new_edit_stagiaire(FlashyNotifier $flashy, Stagiaire $stagiaire = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if(!$stagiaire){
            $stagiaire = new Stagiaire();
        }
        $formStagiaire = $this->createForm(StagiaireType::class, $stagiaire);
        $formStagiaire->handleRequest($request);

        if ($formStagiaire->isSubmitted() && $formStagiaire->isValid()) {
            try{
                $stagiaire = $formStagiaire->getData();
                $entityManager->persist($stagiaire);
                $entityManager->flush();
    
                if ($stagiaire->getId()) {
                    $flashy->success("Le profil du stagiaire $stagiaire a été modifé");

                    return $this->redirectToRoute("app_ficheStagiaire", ['id' => $stagiaire->getId()]);
                } else {
                    $flashy->success("Le stagiaire a été ajouté");

                    return $this->redirectToRoute("app_formStagiaire");
                }
    
            }
            catch (\Exception $e){

                if ($stagiaire->getId()) {
                    $flashy->error("Un problème est survenu lors de la modification du profil du stagiaire $stagiaire");

                    return $this->redirectToRoute("app_ficheStagiaire", ['id' => $stagiaire->getId()]);
                } else {
                    $flashy->error("Un problème est survenu lors de l'ajout du stagiaire");

                    return $this->redirectToRoute("app_formStagiaire");
                }
            }

        }
        return $this->render('stagiaire/form.html.twig', [
            'formStagiaire' => $formStagiaire,
            'edit' => $stagiaire->getId()
        ]);
    }

    #[Route('/stagiaire/{id}/delete', name: 'app_deleteStagiaire')]
    public function delete(Stagiaire $stagiaire, EntityManagerInterface $entityManager){

        try{
            $entityManager->remove($stagiaire);
            $entityManager->flush();  

            $flashy->success("Le stagiaire $stagiaire a été supprimé");
        }
        catch (\Exception $e){
            $flashy->error("Un problème est survenu lors de la suppression du stagiaire $stagiaire");
        }

        return $this->redirectToRoute("app_stagiaire");
    }


    #[Route('/stagiaire/{id}', name: 'app_ficheStagiaire')]
    public function fiche(Stagiaire $stagiaire): Response
    {
        return $this->render('stagiaire/fiche.html.twig', [
            'stagiaire' => $stagiaire,
        ]);
    }
}
