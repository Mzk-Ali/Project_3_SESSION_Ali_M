<?php

namespace App\Controller;

use App\Entity\Stagiaire;
use App\Form\StagiaireType;
use App\Repository\StagiaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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
    public function new_edit_stagiaire(Stagiaire $stagiaire = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if(!$stagiaire){
            $stagiaire = $stagiaire = new Stagiaire();
        }
        $formStagiaire = $this->createForm(StagiaireType::class, $stagiaire);
        $formStagiaire->handleRequest($request);

        if ($formStagiaire->isSubmitted() && $formStagiaire->isValid()) {

            $entityManager->persist($stagiaire);
            $entityManager->flush();

            return $this->redirectToRoute("app_formStagiaire");

        }
        return $this->render('stagiaire/form.html.twig', [
            'formStagiaire' => $formStagiaire,
        ]);
    }

    #[Route('/stagiaire/{id}/delete', name: 'app_deleteStagiaire')]
    public function delete(Stagiaire $stagiaire, EntityManagerInterface $entityManager){
        $entityManager->remove($stagiaire);
        $entityManager->flush();
    }


    #[Route('/stagiaire/{id}', name: 'app_ficheStagiaire')]
    public function fiche(Stagiaire $stagiaire): Response
    {
        return $this->render('stagiaire/fiche.html.twig', [
            'stagiaire' => $stagiaire,
        ]);
    }
}
