<?php
namespace App\Controller\Pubs;

use App\Entity\Pubs\Title;
use App\Entity\Pubs\Publisher;
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
    public function list(Request $req, EntityManagerInterface $em): JsonResponse
    {
        $page = max(1, (int)$req->query->get('page', 1));
        $size = min(100, max(1, (int)$req->query->get('pageSize', 20)));
        $offset = ($page - 1) * $size;

        $qb = $em->getRepository(Title::class)->createQueryBuilder('t')
            ->setFirstResult($offset)->setMaxResults($size)
            ->orderBy('t.id', 'ASC');

        if ($q = (string)$req->query->get('search', '')) {
            $qb->andWhere('t.title LIKE :q')->setParameter('q', '%'.$q.'%');
        }

        $items = array_map(function(Title $t){
            return [
                'title_id' => $t->getId(),
                'title'    => $t->getTitle(),
                'type'     => $t->getType(),
                'pub_id'   => $t->getPublisher()?->getId(),
                'price'    => $t->getPrice(),
                'advance'  => $t->getAdvance(),
                'royalty'  => $t->getRoyalty(),
                'ytd_sales'=> $t->getYtdSales(),
                'notes'    => $t->getNotes(),
                'pubdate'  => $t->getPubdate()->format('Y-m-d H:i:s'),
            ];
        }, $qb->getQuery()->getResult());

        return $this->json(['page'=>$page,'pageSize'=>$size,'items'=>$items]);
    }

    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function getOne(EntityManagerInterface $em, string $id): JsonResponse
    {
        $t = $em->find(Title::class, $id);
        if (!$t) return $this->json(['error'=>'Title not found'], 404);

        return $this->json([
            'title_id'=>$t->getId(),
            'title'=>$t->getTitle(),
            'type'=>$t->getType(),
            'pub_id'=>$t->getPublisher()?->getId(),
            'price'=>$t->getPrice(),
            'advance'=>$t->getAdvance(),
            'royalty'=>$t->getRoyalty(),
            'ytd_sales'=>$t->getYtdSales(),
            'notes'=>$t->getNotes(),
            'pubdate'=>$t->getPubdate()->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $req, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($req->getContent(), true) ?? [];

        $entity = new Title();
        $entity->setId($data['title_id'])
               ->setTitle($data['title'])
               ->setType($data['type'] ?? 'UNDECIDED')
               ->setPrice($data['price'] ?? null)
               ->setAdvance($data['advance'] ?? null)
               ->setRoyalty(isset($data['royalty']) ? (int)$data['royalty'] : null)
               ->setYtdSales(isset($data['ytd_sales']) ? (int)$data['ytd_sales'] : null)
               ->setNotes($data['notes'] ?? null);

        // pubdate gönderildiyse parse et; yoksa ctor'daki now kalır
        if (!empty($data['pubdate'])) {
            $entity->setPubdate(new \DateTimeImmutable($data['pubdate']));
        }

        if (!empty($data['pub_id'])) {
            $pub = $em->find(Publisher::class, $data['pub_id']);
            if (!$pub) return $this->json(['error'=>'Publisher not found'], 400);
            $entity->setPublisher($pub);
        }

        $em->persist($entity);
        $em->flush();

        return $this->json(['message'=>'created','id'=>$entity->getId()], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT','PATCH'])]
    public function update(string $id, Request $req, EntityManagerInterface $em): JsonResponse
    {
        $entity = $em->find(Title::class, $id);
        if (!$entity) return $this->json(['error'=>'Title not found'], 404);

        $d = json_decode($req->getContent(), true) ?? [];

        if (isset($d['title']))    $entity->setTitle($d['title']);
        if (isset($d['type']))     $entity->setType($d['type']);
        if (array_key_exists('price', $d))   $entity->setPrice($d['price']);
        if (array_key_exists('advance', $d)) $entity->setAdvance($d['advance']);
        if (array_key_exists('royalty', $d)) $entity->setRoyalty($d['royalty'] === null ? null : (int)$d['royalty']);
        if (array_key_exists('ytd_sales',$d))$entity->setYtdSales($d['ytd_sales'] === null ? null : (int)$d['ytd_sales']);
        if (array_key_exists('notes', $d))   $entity->setNotes($d['notes']);
        if (!empty($d['pubdate'])) $entity->setPubdate(new \DateTimeImmutable($d['pubdate']));

        if (array_key_exists('pub_id', $d)) {
            if ($d['pub_id'] === null || $d['pub_id'] === '') {
                $entity->setPublisher(null);
            } else {
                $pub = $em->find(Publisher::class, $d['pub_id']);
                if (!$pub) return $this->json(['error'=>'Publisher not found'], 400);
                $entity->setPublisher($pub);
            }
        }

        $em->flush();
        return $this->json(['message'=>'updated']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id, EntityManagerInterface $em): JsonResponse
    {
        $entity = $em->find(Title::class, $id);
        if (!$entity) return $this->json(['error'=>'Title not found'], 404);

        $em->remove($entity);
        $em->flush();
        return $this->json(['message'=>'deleted']);
    }
}
