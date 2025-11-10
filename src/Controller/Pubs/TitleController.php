<?php
namespace App\Controller\Pubs;

use App\Entity\Pubs\Title;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/pubs/titles', name: 'titles_')]
class TitleController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        $titles = $em->getRepository(Title::class)->findAll();
        return $this->json($titles);
    }

    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function getOne(EntityManagerInterface $em, string $id): JsonResponse
    {
        $title = $em->getRepository(Title::class)->find($id);
        if (!$title) {
            return $this->json(['error' => 'Title not found'], 404);
        }
        return $this->json($title);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $req, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($req->getContent(), true);
        $title = (new Title())
            ->setId($data['title_id'])
            ->setTitle($data['title'])
            ->setType($data['type'] ?? 'UNDECIDED')
            ->setPubId($data['pub_id'] ?? null)
            ->setPrice($data['price'] ?? null)
            ->setAdvance($data['advance'] ?? null)
            ->setRoyalty($data['royalty'] ?? null)
            ->setYtdSales($data['ytd_sales'] ?? null)
            ->setNotes($data['notes'] ?? null)
            ->setPubdate(new \DateTime($data['pubdate'] ?? 'now'));

        $em->persist($title);
        $em->flush();

        return $this->json($title, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    public function update(Request $req, EntityManagerInterface $em, string $id): JsonResponse
    {
        $title = $em->getRepository(Title::class)->find($id);
        if (!$title) {
            return $this->json(['error' => 'Title not found'], 404);
        }

        $data = json_decode($req->getContent(), true);
        foreach ($data as $key => $value) {
            $method = match ($key) {
                'title_id' => 'setId',
                'title' => 'setTitle',
                'type' => 'setType',
                'pub_id' => 'setPubId',
                'price' => 'setPrice',
                'advance' => 'setAdvance',
                'royalty' => 'setRoyalty',
                'ytd_sales' => 'setYtdSales',
                'notes' => 'setNotes',
                'pubdate' => 'setPubdate',
                default => null,
            };
            if ($method) {
                if ($method === 'setPubdate') $value = new \DateTime($value);
                $title->$method($value);
            }
        }

        $em->flush();
        return $this->json($title);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $em, string $id): JsonResponse
    {
        $title = $em->getRepository(Title::class)->find($id);
        if (!$title) {
            return $this->json(['error' => 'Title not found'], 404);
        }

        $em->remove($title);
        $em->flush();
        return $this->json(['message' => 'Title deleted']);
    }
}
