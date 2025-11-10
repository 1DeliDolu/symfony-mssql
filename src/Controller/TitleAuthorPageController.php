<?php
namespace App\Controller;

use App\Repository\TitleAuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/title-authors', name: 'title_author_page_')]
class TitleAuthorPageController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(TitleAuthorRepository $repository): Response
    {
        return $this->render('title_author/index.html.twig', [
            'title_authors' => $repository->findAll(),
        ]);
    }

    #[Route('/{auId}/{titleId}', name: 'show', methods: ['GET'])]
    public function show(TitleAuthorRepository $repository, string $auId, string $titleId): Response
    {
        $relation = $repository->find(['auId' => $auId, 'titleId' => $titleId]);
        if (!$relation) {
            throw $this->createNotFoundException('Relation not found.');
        }

        return $this->render('title_author/show.html.twig', [
            'title_author' => $relation,
        ]);
    }
}

