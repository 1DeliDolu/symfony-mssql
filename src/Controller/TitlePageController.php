<?php
namespace App\Controller;

use App\Repository\TitleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}

