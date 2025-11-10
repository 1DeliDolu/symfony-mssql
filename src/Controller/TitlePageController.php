<?php
namespace App\Controller;

use App\Form\TitleType;
use App\Repository\TitleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/titles', name: 'title_page_')]
class TitlePageController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(TitleRepository $titleRepository): Response
    {
        return $this->render('title/index.html.twig', [
            'titles' => $titleRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id, TitleRepository $titleRepository): Response
    {
        $title = $titleRepository->find($id);
        if (!$title) {
            throw $this->createNotFoundException('Title not found.');
        }

        return $this->render('title/show.html.twig', [
            'title' => $title,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET','POST'])]
    public function edit(
        Request $request,
        string $id,
        TitleRepository $titleRepository,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $em
    ): Response
    {
        $title = $titleRepository->find($id);
        if (!$title) {
            throw $this->createNotFoundException('Title not found.');
        }

        $form = $formFactory->create(TitleType::class, $title, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($title);
            $em->flush();

            return $this->redirectToRoute('title_page_show', ['id' => $title->getId()]);
        }

        return $this->render('title/edit.html.twig', [
            'title' => $title,
            'form' => $form->createView(),
        ]);
    }
}
