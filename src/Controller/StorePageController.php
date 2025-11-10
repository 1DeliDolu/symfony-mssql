<?php
namespace App\Controller;

use App\Form\StoreType;
use App\Repository\StoreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stores', name: 'store_page_')]
class StorePageController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(StoreRepository $storeRepository): Response
    {
        return $this->render('store/index.html.twig', [
            'stores' => $storeRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id, StoreRepository $storeRepository): Response
    {
        $store = $storeRepository->find($id);
        if (!$store) {
            throw $this->createNotFoundException('Store not found.');
        }

        return $this->render('store/show.html.twig', [
            'store' => $store,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET','POST'])]
    public function edit(
        Request $request,
        string $id,
        StoreRepository $storeRepository,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $em
    ): Response
    {
        $store = $storeRepository->find($id);
        if (!$store) {
            throw $this->createNotFoundException('Store not found.');
        }

        $form = $formFactory->create(StoreType::class, $store, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($store);
            $em->flush();

            return $this->redirectToRoute('store_page_show', ['id' => $store->getId()]);
        }

        return $this->render('store/edit.html.twig', [
            'store' => $store,
            'form' => $form->createView(),
        ]);
    }
}
