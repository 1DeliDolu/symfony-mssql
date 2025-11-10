<?php
namespace App\Controller\Pubs;

use App\Entity\Pubs\Author;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/pubs/authors', name: 'authors_')]
class AuthorController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        $authors = $em->getRepository(Author::class)->findAll();
        return $this->json($authors);
    }

    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function getOne(EntityManagerInterface $em, string $id): JsonResponse
    {
        $author = $em->getRepository(Author::class)->find($id);
        if (!$author) {
            return $this->json(['error' => 'Author not found'], 404);
        }
        return $this->json($author);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $req, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($req->getContent(), true);
        $author = new Author();
        $author
            ->setId($data['au_id'])
            ->setFirstName($data['au_fname'])
            ->setLastName($data['au_lname'])
            ->setPhone($data['phone'] ?? 'UNKNOWN')
            ->setAddress($data['address'] ?? null)
            ->setCity($data['city'] ?? null)
            ->setState($data['state'] ?? null)
            ->setZip($data['zip'] ?? null)
            ->setContract($data['contract'] ?? false);

        $em->persist($author);
        $em->flush();

        return $this->json($author, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    public function update(Request $req, EntityManagerInterface $em, string $id): JsonResponse
    {
        $author = $em->getRepository(Author::class)->find($id);
        if (!$author) {
            return $this->json(['error' => 'Author not found'], 404);
        }

        $data = json_decode($req->getContent(), true);
        foreach ($data as $key => $value) {
            $method = match ($key) {
                'au_id' => 'setId',
                'au_lname' => 'setLastName',
                'au_fname' => 'setFirstName',
                'phone' => 'setPhone',
                'address' => 'setAddress',
                'city' => 'setCity',
                'state' => 'setState',
                'zip' => 'setZip',
                'contract' => 'setContract',
                default => null,
            };
            if ($method) $author->$method($value);
        }

        $em->flush();
        return $this->json($author);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $em, string $id): JsonResponse
    {
        $author = $em->getRepository(Author::class)->find($id);
        if (!$author) {
            return $this->json(['error' => 'Author not found'], 404);
        }

        $em->remove($author);
        $em->flush();
        return $this->json(['message' => 'Author deleted']);
    }
}
