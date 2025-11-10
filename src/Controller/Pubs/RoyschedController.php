<?php
namespace App\Controller\Pubs;

use App\Entity\Pubs\Roysched;
use App\Entity\Pubs\Title;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/pubs/roysched', name: 'roysched_')]
class RoyschedController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $req, EntityManagerInterface $em): JsonResponse
    {
        $repo = $em->getRepository(Roysched::class);
        $qb = $repo->createQueryBuilder('r')
            ->leftJoin('r.title', 't')->addSelect('t')
            ->orderBy('r.titleId', 'ASC');

        if ($tid = $req->query->get('title_id')) {
            $qb->andWhere('r.titleId = :tid')->setParameter('tid', $tid);
        }

        $items = array_map(fn(Roysched $r) => [
            'title_id' => $r->getTitleId(),
            'lorange'  => $r->getLorange(),
            'hirange'  => $r->getHirange(),
            'royalty'  => $r->getRoyalty(),
        ], $qb->getQuery()->getResult());

        return $this->json($items);
    }

    #[Route('/{title_id}', name: 'get', methods: ['GET'])]
    public function getOne(string $title_id, EntityManagerInterface $em): JsonResponse
    {
        $r = $em->getRepository(Roysched::class)->find($title_id);
        if (!$r) return $this->json(['error' => 'Not found'], 404);

        return $this->json([
            'title_id' => $r->getTitleId(),
            'lorange'  => $r->getLorange(),
            'hirange'  => $r->getHirange(),
            'royalty'  => $r->getRoyalty(),
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $req, EntityManagerInterface $em): JsonResponse
    {
        $d = json_decode($req->getContent(), true) ?? [];
        foreach (['title_id'] as $k)
            if (empty($d[$k])) return $this->json(['error' => "$k required"], 400);

        $title = $em->find(Title::class, $d['title_id']);
        if (!$title) return $this->json(['error' => 'Title not found'], 400);

        $r = new Roysched();
        $r->setTitleId($d['title_id'])
          ->setTitle($title)
          ->setLorange($d['lorange'] ?? null)
          ->setHirange($d['hirange'] ?? null)
          ->setRoyalty($d['royalty'] ?? null);

        $em->persist($r);
        $em->flush();

        return $this->json(['message' => 'created'], Response::HTTP_CREATED);
    }

    #[Route('/{title_id}', name: 'update', methods: ['PUT','PATCH'])]
    public function update(string $title_id, Request $req, EntityManagerInterface $em): JsonResponse
    {
        $r = $em->getRepository(Roysched::class)->find($title_id);
        if (!$r) return $this->json(['error' => 'Not found'], 404);

        $d = json_decode($req->getContent(), true) ?? [];
        if (array_key_exists('lorange', $d)) $r->setLorange($d['lorange']);
        if (array_key_exists('hirange', $d)) $r->setHirange($d['hirange']);
        if (array_key_exists('royalty', $d)) $r->setRoyalty($d['royalty']);

        $em->flush();
        return $this->json(['message' => 'updated']);
    }

    #[Route('/{title_id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $title_id, EntityManagerInterface $em): JsonResponse
    {
        $r = $em->getRepository(Roysched::class)->find($title_id);
        if (!$r) return $this->json(['error' => 'Not found'], 404);

        $em->remove($r);
        $em->flush();
        return $this->json(['message' => 'deleted']);
    }
}
