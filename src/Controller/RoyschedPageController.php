<?php
namespace App\Controller;

use App\Form\RoyschedType;
use App\Repository\RoyschedRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/roysched', name: 'roysched_page_')]
class RoyschedPageController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(RoyschedRepository $repo): Response
    {
        return $this->render('roysched/index.html.twig', [
            'royscheds' => $repo->findAll(),
        ]);
    }

    #[Route('/{titleId}', name: 'show', methods: ['GET'])]
    public function show(string $titleId, RoyschedRepository $repo): Response
    {
        $schedule = $repo->find($titleId);
        if (!$schedule) {
            throw $this->createNotFoundException('Royalty schedule not found.');
        }

        return $this->render('roysched/show.html.twig', [
            'roysched' => $schedule,
        ]);
    }

    #[Route('/{titleId}/edit', name: 'edit', methods: ['GET','POST'])]
    public function edit(
        string $titleId,
        Request $request,
        RoyschedRepository $repo,
        EntityManagerInterface $em
    ): Response {
        $schedule = $repo->find($titleId);
        if (!$schedule) {
            throw $this->createNotFoundException('Royalty schedule not found.');
        }

        $form = $this->createForm(RoyschedType::class, $schedule, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('roysched_page_show', ['titleId' => $schedule->getTitleId()]);
        }

        return $this->render('roysched/edit.html.twig', [
            'roysched' => $schedule,
            'form' => $form->createView(),
        ]);
    }
}
