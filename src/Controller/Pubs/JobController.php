<?php
namespace App\Controller\Pubs;

use App\Entity\Pubs\Job;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/pubs/jobs', name: 'jobs_')]
class JobController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $req, EntityManagerInterface $em): JsonResponse
    {
        $page = max(1, (int)$req->query->get('page', 1));
        $size = min(100, max(1, (int)$req->query->get('pageSize', 20)));
        $offset = ($page - 1) * $size;

        $qb = $em->getRepository(Job::class)->createQueryBuilder('j')
            ->orderBy('j.id', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($size);

        if ($q = (string)$req->query->get('search', '')) {
            $qb->andWhere('j.description LIKE :q')->setParameter('q', "%$q%");
        }

        $items = array_map(fn(Job $j) => [
            'job_id'  => $j->getId(),
            'job_desc'=> $j->getDescription(),
            'min_lvl' => $j->getMinLvl(),
            'max_lvl' => $j->getMaxLvl(),
        ], $qb->getQuery()->getResult());

        return $this->json(['page'=>$page,'pageSize'=>$size,'items'=>$items]);
    }

    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function getOne(int $id, EntityManagerInterface $em): JsonResponse
    {
        $j = $em->find(Job::class, $id);
        if (!$j) return $this->json(['error'=>'Job not found'], 404);

        return $this->json([
            'job_id'  => $j->getId(),
            'job_desc'=> $j->getDescription(),
            'min_lvl' => $j->getMinLvl(),
            'max_lvl' => $j->getMaxLvl(),
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $req, EntityManagerInterface $em): JsonResponse
    {
        $d = json_decode($req->getContent(), true) ?? [];

        // Basit doğrulama (tabloda CHECK var: min>=10, max<=250)
        if (!isset($d['min_lvl'], $d['max_lvl'])) {
            return $this->json(['error'=>'min_lvl and max_lvl required'], 400);
        }
        $min = (int)$d['min_lvl']; $max = (int)$d['max_lvl'];
        if ($min < 10)  return $this->json(['error'=>'min_lvl must be >= 10'], 422);
        if ($max > 250) return $this->json(['error'=>'max_lvl must be <= 250'], 422);

        $j = new Job();
        $j->setDescription($d['job_desc'] ?? 'New Position - title not formalized yet')
          ->setMinLvl($min)
          ->setMaxLvl($max);

        $em->persist($j);
        $em->flush(); // IDENTITY → job_id otomatik gelir

        return $this->json([
            'message'=>'created',
            'job_id'=>$j->getId()
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT','PATCH'])]
    public function update(int $id, Request $req, EntityManagerInterface $em): JsonResponse
    {
        $j = $em->find(Job::class, $id);
        if (!$j) return $this->json(['error'=>'Job not found'], 404);

        $d = json_decode($req->getContent(), true) ?? [];

        if (array_key_exists('job_desc', $d)) $j->setDescription($d['job_desc']);

        if (array_key_exists('min_lvl', $d)) {
            $min = (int)$d['min_lvl'];
            if ($min < 10) return $this->json(['error'=>'min_lvl must be >= 10'], 422);
            $j->setMinLvl($min);
        }

        if (array_key_exists('max_lvl', $d)) {
            $max = (int)$d['max_lvl'];
            if ($max > 250) return $this->json(['error'=>'max_lvl must be <= 250'], 422);
            $j->setMaxLvl($max);
        }

        $em->flush();
        return $this->json(['message'=>'updated']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        $j = $em->find(Job::class, $id);
        if (!$j) return $this->json(['error'=>'Job not found'], 404);

        $em->remove($j);
        $em->flush();
        return $this->json(['message'=>'deleted']);
    }
}
