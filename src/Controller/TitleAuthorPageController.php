<?php
namespace App\Controller;

use App\Form\TitleAuthorType;
use App\Repository\TitleAuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/{auId}/{titleId}/edit', name: 'edit', methods: ['GET','POST'])]
    public function edit(
        TitleAuthorRepository $repository,
        FormFactoryInterface $formFactory,
        Request $request,
        EntityManagerInterface $em,
        string $auId,
        string $titleId
    ): Response
    {
        $relation = $repository->find(['auId' => $auId, 'titleId' => $titleId]);
        if (!$relation) {
            throw $this->createNotFoundException('Relation not found.');
        }

        $form = $formFactory->create(TitleAuthorType::class, $relation, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($relation);
            $em->flush();

            return $this->redirectToRoute('title_author_page_show', [
                'auId' => $relation->getAuId(),
                'titleId' => $relation->getTitleId(),
            ]);
        }

        return $this->render('title_author/edit.html.twig', [
            'title_author' => $relation,
            'form' => $form->createView(),
        ]);
    }
}
