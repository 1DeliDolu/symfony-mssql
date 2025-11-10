<?php
namespace App\Controller;

use App\Form\PubInfoType;
use App\Repository\PubInfoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/pub-info', name: 'pub_info_page_')]
class PubInfoPageController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(PubInfoRepository $repository): Response
    {
        return $this->render('pub_info/index.html.twig', [
            'pub_infos' => $repository->findAll(),
        ]);
    }

    #[Route('/{pubId}', name: 'show', methods: ['GET'])]
    public function show(string $pubId, PubInfoRepository $repository): Response
    {
        $info = $repository->find($pubId);
        if (!$info) {
            throw $this->createNotFoundException('Publisher info not found.');
        }

        return $this->render('pub_info/show.html.twig', [
            'pub_info' => $info,
        ]);
    }

    #[Route('/{pubId}/edit', name: 'edit', methods: ['GET','POST'])]
    public function edit(
        string $pubId,
        Request $request,
        PubInfoRepository $repository,
        EntityManagerInterface $em
    ): Response {
        $info = $repository->find($pubId);
        if (!$info) {
            throw $this->createNotFoundException('Publisher info not found.');
        }

        $form = $this->createForm(PubInfoType::class, $info, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('pub_info_page_show', ['pubId' => $info->getPubId()]);
        }

        return $this->render('pub_info/edit.html.twig', [
            'pub_info' => $info,
            'form' => $form->createView(),
        ]);
    }
}
