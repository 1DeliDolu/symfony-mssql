<?php
namespace App\Controller;

use App\Entity\Pubs\Discount;
use App\Form\DiscountType;
use App\Repository\DiscountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/discounts', name: 'discount_')]
class DiscountCrudController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(DiscountRepository $discountRepository): Response
    {
        return $this->render('discount/index.html.twig', [
            'discounts' => $discountRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $discount = new Discount();
        $form = $this->createForm(DiscountType::class, $discount);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($discount);
            $em->flush();

            $this->addFlash('success', 'Discount created successfully.');

            return $this->redirectToRoute('discount_index');
        }

        return $this->render('discount/new.html.twig', [
            'discount' => $discount,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{discountType}', name: 'show', methods: ['GET'])]
    public function show(string $discountType, DiscountRepository $discountRepository): Response
    {
        $discount = $discountRepository->find($discountType);
        if (!$discount) {
            throw $this->createNotFoundException('Discount not found.');
        }

        return $this->render('discount/show.html.twig', [
            'discount' => $discount,
        ]);
    }

    #[Route('/{discountType}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(string $discountType, Request $request, DiscountRepository $discountRepository, EntityManagerInterface $em): Response
    {
        $discount = $discountRepository->find($discountType);
        if (!$discount) {
            throw $this->createNotFoundException('Discount not found.');
        }

        $form = $this->createForm(DiscountType::class, $discount, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Discount updated successfully.');

            return $this->redirectToRoute('discount_index');
        }

        return $this->render('discount/edit.html.twig', [
            'discount' => $discount,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{discountType}', name: 'delete', methods: ['POST'])]
    public function delete(string $discountType, Request $request, DiscountRepository $discountRepository, EntityManagerInterface $em): Response
    {
        $discount = $discountRepository->find($discountType);
        if (!$discount) {
            throw $this->createNotFoundException('Discount not found.');
        }

        if ($this->isCsrfTokenValid('delete' . $discount->getDiscountType(), $request->request->get('_token'))) {
            $em->remove($discount);
            $em->flush();
            $this->addFlash('success', 'Discount deleted.');
        }

        return $this->redirectToRoute('discount_index');
    }
}
