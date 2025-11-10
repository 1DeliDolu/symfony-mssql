<?php
namespace App\Controller;

use App\Entity\Pubs\Publisher;
use App\Form\PublisherType;
use App\Repository\PublisherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/publishers', name: 'publisher_')]
class PublisherCrudController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(PublisherRepository $publisherRepository): Response
    {
        return $this->render('publisher/index.html.twig', [
            'publishers' => $publisherRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $publisher = new Publisher();
        $form = $this->createForm(PublisherType::class, $publisher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($publisher);
            $em->flush();

            $this->addFlash('success', 'Publisher created successfully.');

            return $this->redirectToRoute('publisher_index');
        }

        return $this->render('publisher/new.html.twig', [
            'publisher' => $publisher,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id, PublisherRepository $publisherRepository): Response
    {
        $publisher = $publisherRepository->find($id);
        if (!$publisher) {
            throw $this->createNotFoundException('Publisher not found.');
        }

        return $this->render('publisher/show.html.twig', [
            'publisher' => $publisher,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(string $id, Request $request, PublisherRepository $publisherRepository, EntityManagerInterface $em): Response
    {
        $publisher = $publisherRepository->find($id);
        if (!$publisher) {
            throw $this->createNotFoundException('Publisher not found.');
        }

        $form = $this->createForm(PublisherType::class, $publisher, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Publisher updated successfully.');

            return $this->redirectToRoute('publisher_index');
        }

        return $this->render('publisher/edit.html.twig', [
            'publisher' => $publisher,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(string $id, Request $request, PublisherRepository $publisherRepository, EntityManagerInterface $em): Response
    {
        $publisher = $publisherRepository->find($id);
        if (!$publisher) {
            throw $this->createNotFoundException('Publisher not found.');
        }

        if ($this->isCsrfTokenValid('delete' . $publisher->getId(), $request->request->get('_token'))) {
            $em->remove($publisher);
            $em->flush();
            $this->addFlash('success', 'Publisher deleted.');
        }

        return $this->redirectToRoute('publisher_index');
    }
}

