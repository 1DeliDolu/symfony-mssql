<?php
namespace App\Controller\Pubs;

use App\Entity\Pubs\PubInfo;
use App\Entity\Pubs\Publisher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/pubs/pubinfo', name: 'pubinfo_')]
class PubInfoController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        $repo = $em->getRepository(PubInfo::class);
        $list = array_map(fn(PubInfo $p) => [
            'pub_id' => $p->getPubId(),
            'pr_info' => $p->getPrInfo(),
            'has_logo' => $p->getLogo() !== null,
        ], $repo->findAll());

        return $this->json($list);
    }

    #[Route('/{pub_id}', name: 'get', methods: ['GET'])]
    public function getOne(string $pub_id, EntityManagerInterface $em): JsonResponse
    {
        $p = $em->find(PubInfo::class, $pub_id);
        if (!$p) return $this->json(['error' => 'Not found'], 404);

        return $this->json([
            'pub_id' => $p->getPubId(),
            'pr_info' => $p->getPrInfo(),
            'has_logo' => $p->getLogo() !== null,
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $req, EntityManagerInterface $em): JsonResponse
    {
        $data = $req->request->all();
        if (empty($data['pub_id'])) return $this->json(['error' => 'pub_id required'], 400);

        $publisher = $em->find(Publisher::class, $data['pub_id']);
        if (!$publisher) return $this->json(['error' => 'Publisher not found'], 404);

        if ($em->find(PubInfo::class, $data['pub_id']))
            return $this->json(['error' => 'Already exists'], 409);

        $info = new PubInfo();
        $info->setPubId($data['pub_id'])
             ->setPublisher($publisher)
             ->setPrInfo($data['pr_info'] ?? null);

        /** @var UploadedFile|null $file */
        $file = $req->files->get('logo');
        if ($file instanceof UploadedFile) {
            $info->setLogo(file_get_contents($file->getRealPath()));
        }

        $em->persist($info);
        $em->flush();

        return $this->json(['message' => 'created'], Response::HTTP_CREATED);
    }

    #[Route('/{pub_id}', name: 'update', methods: ['PUT','PATCH'])]
    public function update(string $pub_id, Request $req, EntityManagerInterface $em): JsonResponse
    {
        $info = $em->find(PubInfo::class, $pub_id);
        if (!$info) return $this->json(['error' => 'Not found'], 404);

        $data = $req->request->all();
        if (isset($data['pr_info'])) $info->setPrInfo($data['pr_info']);

        /** @var UploadedFile|null $file */
        $file = $req->files->get('logo');
        if ($file instanceof UploadedFile) {
            $info->setLogo(file_get_contents($file->getRealPath()));
        }

        $em->flush();
        return $this->json(['message' => 'updated']);
    }

    #[Route('/{pub_id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $pub_id, EntityManagerInterface $em): JsonResponse
    {
        $info = $em->find(PubInfo::class, $pub_id);
        if (!$info) return $this->json(['error' => 'Not found'], 404);

        $em->remove($info);
        $em->flush();
        return $this->json(['message' => 'deleted']);
    }

    #[Route('/{pub_id}/logo', name: 'logo', methods: ['GET'])]
    public function getLogo(string $pub_id, EntityManagerInterface $em): Response
    {
        $info = $em->find(PubInfo::class, $pub_id);
        if (!$info || !$info->getLogo()) return new Response('Not found', 404);

        $response = new Response(stream_get_contents($info->getLogo()));
        $response->headers->set('Content-Type', 'image/png');
        return $response;
    }
}
