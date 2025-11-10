<?php
namespace App\Controller\Pubs;

use App\Entity\Pubs\Publisher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/pubs/publishers', name: 'publishers_')]
class PublisherController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        $items = $em->getRepository(Publisher::class)->findAll();

        return $this->json(array_map(fn(Publisher $p) => [
            'pub_id'   => $p->getId(),
            'pub_name' => $p->getName(),
            'city'     => $p->getCity(),
            'state'    => $p->getState(),
            'country'  => $p->getCountry(),
        ], $items));
    }

    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function getOne(string $id, EntityManagerInterface $em): JsonResponse
    {
        $p = $em->find(Publisher::class, $id);
        if (!$p) return $this->json(['error' => 'Publisher not found'], 404);

        return $this->json([
            'pub_id'   => $p->getId(),
            'pub_name' => $p->getName(),
            'city'     => $p->getCity(),
            'state'    => $p->getState(),
            'country'  => $p->getCountry(),
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $req, EntityManagerInterface $em): JsonResponse
    {
        $d = json_decode($req->getContent(), true) ?? [];
        if (empty($d['pub_id'])) return $this->json(['error'=>'pub_id required'], 400);

        $p = new Publisher();
        $p->setId($d['pub_id'])
          ->setName($d['pub_name'] ?? null)
          ->setCity($d['city'] ?? null)
          ->setState($d['state'] ?? null)
          ->setCountry($d['country'] ?? 'USA');

        $em->persist($p);
        $em->flush();

        return $this->json(['message'=>'created','id'=>$p->getId()], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT','PATCH'])]
    public function update(string $id, Request $req, EntityManagerInterface $em): JsonResponse
    {
        $p = $em->find(Publisher::class, $id);
        if (!$p) return $this->json(['error'=>'Publisher not found'], 404);

        $d = json_decode($req->getContent(), true) ?? [];
        if (array_key_exists('pub_name', $d)) $p->setName($d['pub_name']);
        if (array_key_exists('city', $d))     $p->setCity($d['city']);
        if (array_key_exists('state', $d))    $p->setState($d['state']);
        if (array_key_exists('country', $d))  $p->setCountry($d['country']);

        $em->flush();
        return $this->json(['message'=>'updated']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id, EntityManagerInterface $em): JsonResponse
    {
        $p = $em->find(Publisher::class, $id);
        if (!$p) return $this->json(['error'=>'Publisher not found'], 404);

        $em->remove($p);
        $em->flush();
        return $this->json(['message'=>'deleted']);
    }
}
