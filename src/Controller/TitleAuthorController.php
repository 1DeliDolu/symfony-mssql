<?php
namespace App\Controller\Pubs;

use App\Entity\Pubs\Author;
use App\Entity\Pubs\Title;
use App\Entity\Pubs\TitleAuthor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/pubs/titleauthors', name: 'ta_')]
class TitleAuthorController extends AbstractController
{
    // Liste + filtreler: by author/title
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $req, EntityManagerInterface $em): JsonResponse
    {
        $repo = $em->getRepository(TitleAuthor::class);
        $qb = $repo->createQueryBuilder('ta')
            ->leftJoin('ta.author', 'a')->addSelect('a')
            ->leftJoin('ta.title', 't')->addSelect('t')
            ->setMaxResults(min(100, (int)$req->query->get('pageSize', 50)));

        if ($au = $req->query->get('au_id')) {
            $qb->andWhere('ta.auId = :au')->setParameter('au', $au);
        }
        if ($tid = $req->query->get('title_id')) {
            $qb->andWhere('ta.titleId = :tid')->setParameter('tid', $tid);
        }

        $rows = array_map(function(TitleAuthor $x){
            return [
                'au_id'     => $x->getAuId(),
                'title_id'  => $x->getTitleId(),
                'au_ord'    => $x->getAuOrd(),
                'royaltyper'=> $x->getRoyaltyper(),
                'author'    => $x->getAuthor()?->getId() ?? null,
                'title'     => $x->getTitle()?->getId() ?? null,
            ];
        }, $qb->getQuery()->getResult());

        return $this->json($rows);
    }

    // Tek kayıt (composite key ile)
    #[Route('/{au_id}/{title_id}', name: 'get', methods: ['GET'])]
    public function getOne(string $au_id, string $title_id, EntityManagerInterface $em): JsonResponse
    {
        $ta = $em->getRepository(TitleAuthor::class)->find([
            'auId' => $au_id,
            'titleId' => $title_id,
        ]);

        if (!$ta) return $this->json(['error'=>'Not found'], 404);

        return $this->json([
            'au_id'     => $ta->getAuId(),
            'title_id'  => $ta->getTitleId(),
            'au_ord'    => $ta->getAuOrd(),
            'royaltyper'=> $ta->getRoyaltyper(),
            'author'    => $ta->getAuthor()?->getId(),
            'title'     => $ta->getTitle()?->getId(),
        ]);
    }

    // CREATE
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $req, EntityManagerInterface $em): JsonResponse
    {
        $d = json_decode($req->getContent(), true) ?? [];

        // Zorunlu alan kontrolü
        foreach (['au_id','title_id'] as $k) {
            if (empty($d[$k])) return $this->json(['error'=>"$k required"], 400);
        }

        // FK’ler
        $author = $em->find(Author::class, $d['au_id']);
        $title  = $em->find(Title::class,  $d['title_id']);
        if (!$author || !$title) {
            return $this->json(['error'=>'Author or Title not found'], 400);
        }

        // daha önce var mı?
        $exists = $em->getRepository(TitleAuthor::class)->find([
            'auId' => $d['au_id'],
            'titleId' => $d['title_id'],
        ]);
        if ($exists) return $this->json(['error'=>'Already exists'], 409);

        $ta = new TitleAuthor();
        $ta->setAuId($d['au_id'])
           ->setTitleId($d['title_id'])
           ->setAuthor($author)
           ->setTitle($title)
           ->setAuOrd(isset($d['au_ord']) ? (int)$d['au_ord'] : null)
           ->setRoyaltyper(isset($d['royaltyper']) ? (int)$d['royaltyper'] : null);

        $em->persist($ta);
        $em->flush();

        return $this->json(['message'=>'created'], Response::HTTP_CREATED);
    }

    // UPDATE (composite key route)
    #[Route('/{au_id}/{title_id}', name: 'update', methods: ['PUT','PATCH'])]
    public function update(string $au_id, string $title_id, Request $req, EntityManagerInterface $em): JsonResponse
    {
        $ta = $em->getRepository(TitleAuthor::class)->find([
            'auId' => $au_id,
            'titleId' => $title_id,
        ]);
        if (!$ta) return $this->json(['error'=>'Not found'], 404);

        $d = json_decode($req->getContent(), true) ?? [];
        if (array_key_exists('au_ord', $d))      $ta->setAuOrd($d['au_ord'] === null ? null : (int)$d['au_ord']);
        if (array_key_exists('royaltyper', $d))  $ta->setRoyaltyper($d['royaltyper'] === null ? null : (int)$d['royaltyper']);

        // İlişki güncellemek istersen:
        if (array_key_exists('au_id', $d) && $d['au_id'] !== $au_id) {
            $author = $em->find(Author::class, $d['au_id']);
            if (!$author) return $this->json(['error'=>'Author not found'], 400);
            $ta->setAuId($d['au_id']);
            $ta->setAuthor($author);
        }
        if (array_key_exists('title_id', $d) && $d['title_id'] !== $title_id) {
            $title = $em->find(Title::class, $d['title_id']);
            if (!$title) return $this->json(['error'=>'Title not found'], 400);
            $ta->setTitleId($d['title_id']);
            $ta->setTitle($title);
        }

        $em->flush();
        return $this->json(['message'=>'updated']);
    }

    // DELETE
    #[Route('/{au_id}/{title_id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $au_id, string $title_id, EntityManagerInterface $em): JsonResponse
    {
        $ta = $em->getRepository(TitleAuthor::class)->find([
            'auId' => $au_id,
            'titleId' => $title_id,
        ]);
        if (!$ta) return $this->json(['error'=>'Not found'], 404);

        $em->remove($ta);
        $em->flush();
        return $this->json(['message'=>'deleted']);
    }
}
