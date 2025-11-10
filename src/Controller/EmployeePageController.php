<?php
namespace App\Controller;

use App\Form\EmployeeType;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/employees', name: 'employee_page_')]
class EmployeePageController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(EmployeeRepository $repository): Response
    {
        return $this->render('employee/index.html.twig', [
            'employees' => $repository->findAll(),
        ]);
    }

    #[Route('/{empId}', name: 'show', methods: ['GET'])]
    public function show(string $empId, EmployeeRepository $repository): Response
    {
        $employee = $repository->find($empId);
        if (!$employee) {
            throw $this->createNotFoundException('Employee not found.');
        }

        return $this->render('employee/show.html.twig', [
            'employee' => $employee,
        ]);
    }

    #[Route('/{empId}/edit', name: 'edit', methods: ['GET','POST'])]
    public function edit(
        string $empId,
        Request $request,
        EmployeeRepository $repository,
        EntityManagerInterface $em
    ): Response {
        $employee = $repository->find($empId);
        if (!$employee) {
            throw $this->createNotFoundException('Employee not found.');
        }

        $form = $this->createForm(EmployeeType::class, $employee, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('employee_page_show', ['empId' => $employee->getEmpId()]);
        }

        return $this->render('employee/edit.html.twig', [
            'employee' => $employee,
            'form' => $form->createView(),
        ]);
    }
}
