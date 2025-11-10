<?php
namespace App\Controller\Pubs;

use App\Entity\Pubs\Discount;
use App\Entity\Pubs\Store;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/pubs/discounts', name: 'discounts_')]
class DiscountController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $req, EntityManagerInterface $em): JsonResponse
    {
        $qb = $em->getRepository(Discount::class)->createQueryBuilder('d')
            ->leftJoin('d.store', 's')->addSelect('s')
            ->orderBy('d.discountType', 'ASC');

        if ($stor = $req->query->get('stor_id')) {
            $qb->andWhere('s.id = :sid')->setParameter('sid', $stor);
        }

        $items = array_map(fn(Discount $d) => [
            'discounttype' => $d->getDiscountType(),
            'stor_id'      => $d->getStore()?->getId(),
            'lowqty'       => $d->getLowQty(),
            'highqty'      => $d->getHighQty(),
            'discount'     => $d->getDiscount(),
        ], $qb->getQuery()->getResult());

        return $this->json($items);
    }

    #[Route('/{discounttype}', name: 'get', methods: ['GET'])]
    public function getOne(string $discounttype, EntityManagerInterface $em): JsonResponse
    {
        $d = $em->getRepository(Discount::class)->find($discounttype);
        if (!$d) return $this->json(['error' => 'Not found'], 404);

        return $this->json([
            'discounttype' => $d->getDiscountType(),
            'stor_id'      => $d->getStore()?->getId(),
            'lowqty'       => $d->getLowQty(),
            'highqty'      => $d->getHighQty(),
            'discount'     => $d->getDiscount(),
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $req, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($req->getContent(), true) ?? [];
        if (empty($data['discounttype']) || !isset($data['discount']))
            return $this->json(['error' => 'discounttype and discount required'], 400);

        if ($em->getRepository(Discount::class)->find($data['discounttype']))
            return $this->json(['error' => 'Already exists'], 409);

        $store = null;
        if (!empty($data['stor_id'])) {
            $store = $em->find(Store::class, $data['stor_id']);
            if (!$store) return $this->json(['error' => 'Store not found'], 400);
        }

        $d = new Discount();
        $d->setDiscountType($data['discounttype'])
          ->setStore($store)
          ->setLowQty($data['lowqty'] ?? null)
          ->setHighQty($data['highqty'] ?? null)
          ->setDiscount((float)$data['discount']);

        $em->persist($d);
        $em->flush();

        return $this->json(['message' => 'created'], Response::HTTP_CREATED);
    }

    #[Route('/{discounttype}', name: 'update', methods: ['PUT','PATCH'])]
    public function update(string $discounttype, Request $req, EntityManagerInterface $em): JsonResponse
    {
        $d = $em->getRepository(Discount::class)->find($discounttype);
        if (!$d) return $this->json(['error' => 'Not found'], 404);

        $data = json_decode($req->getContent(), true) ?? [];
        if (array_key_exists('lowqty', $data)) $d->setLowQty($data['lowqty']);
        if (array_key_exists('highqty', $data)) $d->setHighQty($data['highqty']);
        if (array_key_exists('discount', $data)) $d->setDiscount((float)$data['discount']);

        if (array_key_exists('stor_id', $data)) {
            $store = $data['stor_id'] ? $em->find(Store::class, $data['stor_id']) : null;
            $d->setStore($store);
        }

        $em->flush();
        return $this->json(['message' => 'updated']);
    }

    #[Route('/{discounttype}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $discounttype, EntityManagerInterface $em): JsonResponse
    {
        $d = $em->getRepository(Discount::class)->find($discounttype);
        if (!$d) return $this->json(['error' => 'Not found'], 404);

        $em->remove($d);
        $em->flush();
        return $this->json(['message' => 'deleted']);
    }
}
