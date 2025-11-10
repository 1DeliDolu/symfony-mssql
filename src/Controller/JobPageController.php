<?php
namespace App\Controller;

use App\Form\JobType;
use App\Repository\JobRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/jobs', name: 'job_page_')]
class JobPageController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(JobRepository $jobRepository): Response
    {
        return $this->render('job/index.html.twig', [
            'jobs' => $jobRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id, JobRepository $jobRepository): Response
    {
        $job = $jobRepository->find($id);
        if (!$job) {
            throw $this->createNotFoundException('Job not found.');
        }

        return $this->render('job/show.html.twig', [
            'job' => $job,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET','POST'])]
    public function edit(
        int $id,
        Request $request,
        JobRepository $jobRepository,
        EntityManagerInterface $em
    ): Response {
        $job = $jobRepository->find($id);
        if (!$job) {
            throw $this->createNotFoundException('Job not found.');
        }

        $form = $this->createForm(JobType::class, $job, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('job_page_show', ['id' => $job->getId()]);
        }

        return $this->render('job/edit.html.twig', [
            'job' => $job,
            'form' => $form->createView(),
        ]);
    }
}
