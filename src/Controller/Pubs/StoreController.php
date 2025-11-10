<?php
namespace App\Controller\Pubs;

use App\Entity\Pubs\Store;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/pubs/stores', name: 'stores_')]
class StoreController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $req, EntityManagerInterface $em): JsonResponse
    {
        $page = max(1, (int)$req->query->get('page', 1));
        $size = min(100, max(1, (int)$req->query->get('pageSize', 20)));
        $offset = ($page - 1) * $size;

        $qb = $em->getRepository(Store::class)->createQueryBuilder('s')
            ->orderBy('s.id', 'ASC')->setFirstResult($offset)->setMaxResults($size);

        if ($q = (string)$req->query->get('search', '')) {
            $qb->andWhere('s.name LIKE :q OR s.city LIKE :q OR s.state LIKE :q')
               ->setParameter('q', "%$q%");
        }

        $items = array_map(fn(Store $s) => [
            'stor_id' => $s->getId(),
            'stor_name' => $s->getName(),
            'stor_address' => $s->getAddress(),
            'city' => $s->getCity(),
            'state' => $s->getState(),
            'zip' => $s->getZip(),
        ], $qb->getQuery()->getResult());

        return $this->json(['page'=>$page,'pageSize'=>$size,'items'=>$items]);
    }

    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function getOne(string $id, EntityManagerInterface $em): JsonResponse
    {
        $s = $em->find(Store::class, $id);
        if (!$s) return $this->json(['error'=>'Store not found'], 404);

        return $this->json([
            'stor_id' => $s->getId(),
            'stor_name' => $s->getName(),
            'stor_address' => $s->getAddress(),
            'city' => $s->getCity(),
            'state' => $s->getState(),
            'zip' => $s->getZip(),
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $req, EntityManagerInterface $em): JsonResponse
    {
        $d = json_decode($req->getContent(), true) ?? [];
        if (empty($d['stor_id'])) return $this->json(['error'=>'stor_id required'], 400);

        $s = new Store();
        $s->setId($d['stor_id'])
          ->setName($d['stor_name'] ?? null)
          ->setAddress($d['stor_address'] ?? null)
          ->setCity($d['city'] ?? null)
          ->setState($d['state'] ?? null)
          ->setZip($d['zip'] ?? null);

        $em->persist($s);
        $em->flush();

        return $this->json(['message'=>'created','id'=>$s->getId()], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT','PATCH'])]
    public function update(string $id, Request $req, EntityManagerInterface $em): JsonResponse
    {
        $s = $em->find(Store::class, $id);
        if (!$s) return $this->json(['error'=>'Store not found'], 404);

        $d = json_decode($req->getContent(), true) ?? [];
        if (array_key_exists('stor_name', $d))    $s->setName($d['stor_name']);
        if (array_key_exists('stor_address', $d)) $s->setAddress($d['stor_address']);
        if (array_key_exists('city', $d))         $s->setCity($d['city']);
        if (array_key_exists('state', $d))        $s->setState($d['state']);
        if (array_key_exists('zip', $d))          $s->setZip($d['zip']);

        $em->flush();
        return $this->json(['message'=>'updated']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id, EntityManagerInterface $em): JsonResponse
    {
        $s = $em->find(Store::class, $id);
        if (!$s) return $this->json(['error'=>'Store not found'], 404);

        $em->remove($s);
        $em->flush();
        return $this->json(['message'=>'deleted']);
    }
}
