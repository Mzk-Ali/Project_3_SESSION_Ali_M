<?php

namespace App\Controller;

use App\Entity\Formateur;
use App\Form\FormateurType;
use App\Repository\SessionRepository;
use App\Repository\FormateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/formateur/{$id}', name: 'app_ficheFormateur')]
    public function fiche(Formateur $formateur): Response
    {
        return $this->render('formateur/fiche.html.twig', [
            'formateur' => $formateur,
        ]);
    }

    #[Route('/formateur/form', name: 'app_formFormateur')]
    public function formFormateur(Request $request, EntityManagerInterface $entityManager): Response
    {
        $formateur = new Formateur();
        $formFormateur = $this->createForm(FormateurType::class, $formateur);
        $formFormateur->handleRequest($request);

        if ($formFormateur->isSubmitted() && $formFormateur->isValid()) {

            $entityManager->persist($formateur);
            $entityManager->flush();

            return $this->redirectToRoute("app_formFormateur");

        }

        return $this->render('formateur/form.html.twig', [
            'formFormateur' => $formFormateur,
        ]);
    }
}
