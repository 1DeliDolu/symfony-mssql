<?php
namespace App\Controller\Pubs;

use App\Entity\Pubs\Sale;
use App\Entity\Pubs\Store;
use App\Entity\Pubs\Title;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/pubs/sales', name: 'sales_')]
class SaleController extends AbstractController
{
    // LIST with filters: stor_id / ord_num / title_id / date range
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $req, EntityManagerInterface $em): JsonResponse
    {
        $repo = $em->getRepository(Sale::class);
        $qb = $repo->createQueryBuilder('s')
            ->leftJoin('s.store', 'st')->addSelect('st')
            ->leftJoin('s.title', 't')->addSelect('t')
            ->orderBy('s.ordDate', 'DESC')
            ->setMaxResults(min(100, (int)$req->query->get('pageSize', 50)));

        if ($v = $req->query->get('stor_id'))  $qb->andWhere('s.storId = :stor')->setParameter('stor', $v);
        if ($v = $req->query->get('ord_num'))  $qb->andWhere('s.ordNum = :ord')->setParameter('ord', $v);
        if ($v = $req->query->get('title_id')) $qb->andWhere('s.titleId = :tid')->setParameter('tid', $v);

        if ($from = $req->query->get('from'))  $qb->andWhere('s.ordDate >= :from')->setParameter('from', new \DateTimeImmutable($from));
        if ($to   = $req->query->get('to'))    $qb->andWhere('s.ordDate <= :to')->setParameter('to', new \DateTimeImmutable($to));

        $rows = array_map(fn(Sale $x) => [
            'stor_id'  => $x->getStorId(),
            'ord_num'  => $x->getOrdNum(),
            'title_id' => $x->getTitleId(),
            'ord_date' => $x->getOrdDate()->format('Y-m-d H:i:s'),
            'qty'      => $x->getQty(),
            'payterms' => $x->getPayterms(),
        ], $qb->getQuery()->getResult());

        return $this->json($rows);
    }

    // GET by composite key
    #[Route('/{stor_id}/{ord_num}/{title_id}', name: 'get', methods: ['GET'])]
    public function getOne(string $stor_id, string $ord_num, string $title_id, EntityManagerInterface $em): JsonResponse
    {
        $s = $em->getRepository(Sale::class)->find([
            'storId' => $stor_id,
            'ordNum' => $ord_num,
            'titleId'=> $title_id,
        ]);
        if (!$s) return $this->json(['error'=>'Not found'], 404);

        return $this->json([
            'stor_id'  => $s->getStorId(),
            'ord_num'  => $s->getOrdNum(),
            'title_id' => $s->getTitleId(),
            'ord_date' => $s->getOrdDate()->format('Y-m-d H:i:s'),
            'qty'      => $s->getQty(),
            'payterms' => $s->getPayterms(),
        ]);
    }

    // CREATE
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $req, EntityManagerInterface $em): JsonResponse
    {
        $d = json_decode($req->getContent(), true) ?? [];

        foreach (['stor_id','ord_num','title_id','ord_date','qty','payterms'] as $k) {
            if (!isset($d[$k]) || $d[$k] === '') return $this->json(['error'=>"$k required"], 400);
        }

        // FK’ler
        $store = $em->find(Store::class, $d['stor_id']);
        $title = $em->find(Title::class,  $d['title_id']);
        if (!$store || !$title) return $this->json(['error'=>'Store or Title not found'], 400);

        // Zaten var mı?
        $exists = $em->getRepository(Sale::class)->find([
            'storId' => $d['stor_id'],
            'ordNum' => $d['ord_num'],
            'titleId'=> $d['title_id'],
        ]);
        if ($exists) return $this->json(['error'=>'Already exists'], 409);

        $s = new Sale();
        $s->setStorId($d['stor_id'])
          ->setOrdNum($d['ord_num'])
          ->setTitleId($d['title_id'])
          ->setStore($store)
          ->setTitle($title)
          ->setOrdDate(new \DateTimeImmutable($d['ord_date']))
          ->setQty((int)$d['qty'])
          ->setPayterms($d['payterms']);

        $em->persist($s);
        $em->flush();

        return $this->json(['message'=>'created'], Response::HTTP_CREATED);
    }

    // UPDATE
    #[Route('/{stor_id}/{ord_num}/{title_id}', name: 'update', methods: ['PUT','PATCH'])]
    public function update(string $stor_id, string $ord_num, string $title_id, Request $req, EntityManagerInterface $em): JsonResponse
    {
        $s = $em->getRepository(Sale::class)->find([
            'storId' => $stor_id, 'ordNum' => $ord_num, 'titleId' => $title_id
        ]);
        if (!$s) return $this->json(['error'=>'Not found'], 404);

        $d = json_decode($req->getContent(), true) ?? [];

        if (array_key_exists('ord_date', $d) && $d['ord_date'] !== null) $s->setOrdDate(new \DateTimeImmutable($d['ord_date']));
        if (array_key_exists('qty', $d))      $s->setQty((int)$d['qty']);
        if (array_key_exists('payterms', $d)) $s->setPayterms($d['payterms']);

        // (İsteğe bağlı) FK değişimi
        if (array_key_exists('stor_id', $d) && $d['stor_id'] !== $stor_id) {
            $store = $em->find(Store::class, $d['stor_id']);
            if (!$store) return $this->json(['error'=>'Store not found'], 400);
            $s->setStorId($d['stor_id'])->setStore($store);
        }
        if (array_key_exists('title_id', $d) && $d['title_id'] !== $title_id) {
            $title = $em->find(Title::class, $d['title_id']);
            if (!$title) return $this->json(['error'=>'Title not found'], 400);
            $s->setTitleId($d['title_id'])->setTitle($title);
        }

        $em->flush();
        return $this->json(['message'=>'updated']);
    }

    // DELETE
    #[Route('/{stor_id}/{ord_num}/{title_id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $stor_id, string $ord_num, string $title_id, EntityManagerInterface $em): JsonResponse
    {
        $s = $em->getRepository(Sale::class)->find([
            'storId' => $stor_id, 'ordNum' => $ord_num, 'titleId' => $title_id
        ]);
        if (!$s) return $this->json(['error'=>'Not found'], 404);

        $em->remove($s);
        $em->flush();
        return $this->json(['message'=>'deleted']);
    }
}
