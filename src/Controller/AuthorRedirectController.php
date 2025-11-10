<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorRedirectController extends AbstractController
{
    #[Route('/author', name: 'author_redirect', methods: ['GET'])]
    public function __invoke(): Response
    {
        return $this->redirectToRoute('author_index');
    }
}
