<?php

namespace App\Controller;

use App\Entity\Stagiaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StagiaireController extends AbstractController
{
    #[Route('/stagiaire', name: 'app_stagiaire')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $stagiaires = $entityManager->getRepository(Stagiaire::class)->findAll();

        return $this->render('stagiaire/index.html.twig', [
            'stagiaires' => $stagiaires,
        ]);
    }


    // #[Route('/stagiaire', name: 'app_stagiaire')]
    // public function index(EntityManagerInterface $entityManager): Response
    // {
    //     $stagiaires = $entityManager->getRepository(Stagiaire::class)->findAll();

    //     return $this->render('stagiaire/index.html.twig', [
    //         'stagiaires' => $stagiaires,
    //     ]);
    // }
}
