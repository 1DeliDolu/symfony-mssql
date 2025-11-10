<?php
namespace App\Controller;

use App\Entity\Pubs\Sale;
use App\Entity\Pubs\Store;
use App\Entity\Pubs\Title;
use App\Form\SaleType;
use App\Repository\SalesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/sales', name: 'sale_page_')]
class SalePageController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(SalesRepository $salesRepository): Response
    {
        return $this->render('sale/index.html.twig', [
            'sales' => $salesRepository->findAll(),
        ]);
    }

    #[Route('/{storId}/{ordNum}/{titleId}', name: 'show', methods: ['GET'])]
    public function show(
        string $storId,
        string $ordNum,
        string $titleId,
        SalesRepository $salesRepository,
        EntityManagerInterface $em
    ): Response {
        $store = $em->getReference(Store::class, $storId);
        $title = $em->getReference(Title::class, $titleId);

        $sale = $salesRepository->findOneBy([
            'store' => $store,
            'ordNum' => $ordNum,
            'title' => $title,
        ]);

        if (!$sale) {
            throw $this->createNotFoundException('Sale not found.');
        }

        return $this->render('sale/show.html.twig', [
            'sale' => $sale,
        ]);
    }

    #[Route('/{storId}/{ordNum}/{titleId}/edit', name: 'edit', methods: ['GET','POST'])]
    public function edit(
        Request $request,
        string $storId,
        string $ordNum,
        string $titleId,
        SalesRepository $salesRepository,
        EntityManagerInterface $em
    ): Response {
        $store = $em->getReference(Store::class, $storId);
        $title = $em->getReference(Title::class, $titleId);

        $sale = $salesRepository->findOneBy([
            'store' => $store,
            'ordNum' => $ordNum,
            'title' => $title,
        ]);

        if (!$sale) {
            throw $this->createNotFoundException('Sale not found.');
        }

        $form = $this->createForm(SaleType::class, $sale, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('sale_page_show', [
                'storId' => $sale->getStore()->getId(),
                'ordNum' => $sale->getOrdNum(),
                'titleId' => $sale->getTitle()->getId(),
            ]);
        }

        return $this->render('sale/edit.html.twig', [
            'sale' => $sale,
            'form' => $form->createView(),
        ]);
    }
}
