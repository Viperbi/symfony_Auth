<?php

namespace App\Controller;

use App\Entity\Poduct;
use App\Form\PoductType;
use App\Repository\PoductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/poduct')]
final class PoductController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route(name: 'app_poduct_index', methods: ['GET'])]
    public function index(PoductRepository $poductRepository): Response
    {
        return $this->render('poduct/index.html.twig', [
            'poducts' => $poductRepository->findAll(),
        ]);
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/{id}', name: 'app_poduct_show', methods: ['GET'])]
    public function show(Poduct $poduct): Response
    {
        return $this->render('poduct/show.html.twig', [
            'poduct' => $poduct,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/new', name: 'app_poduct_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $poduct = new Poduct();
        $form = $this->createForm(PoductType::class, $poduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($poduct);
            $entityManager->flush();

            return $this->redirectToRoute('app_poduct_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('poduct/new.html.twig', [
            'poduct' => $poduct,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'app_poduct_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Poduct $poduct, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PoductType::class, $poduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_poduct_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('poduct/edit.html.twig', [
            'poduct' => $poduct,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_poduct_delete', methods: ['POST'])]
    public function delete(Request $request, Poduct $poduct, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $poduct->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($poduct);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_poduct_index', [], Response::HTTP_SEE_OTHER);
    }
}
