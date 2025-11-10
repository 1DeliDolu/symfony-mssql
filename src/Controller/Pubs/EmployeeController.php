<?php
namespace App\Controller\Pubs;

use App\Entity\Pubs\Employee;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/employee', name: 'employee_api_')]
class EmployeeController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(EmployeeRepository $employeeRepository): Response
    {
        return $this->render('employee/index.html.twig', [
            'employees' => $employeeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $employee = new Employee();

        // örnek: manuel form verisi işleme
        if ($request->isMethod('POST')) {
            $employee->setEmpId($request->request->get('emp_id'));
            $employee->setFirstName($request->request->get('fname'));
            $employee->setMiddleInitial($request->request->get('minit'));
            $employee->setLastName($request->request->get('lname'));
            $employee->setHireDate(new \DateTime($request->request->get('hire_date')));
            // job ve publisher ilişkileri ayrıca set edilmelidir

            $em->persist($employee);
            $em->flush();

            return $this->redirectToRoute('employee_index');
        }

        return $this->render('employee/new.html.twig', [
            'employee' => $employee,
        ]);
    }

    #[Route('/{emp_id}', name: 'show', methods: ['GET'])]
    public function show(Employee $employee): Response
    {
        return $this->render('employee/show.html.twig', [
            'employee' => $employee,
        ]);
    }

    #[Route('/{emp_id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Employee $employee, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $employee->setFirstName($request->request->get('fname'));
            $employee->setMiddleInitial($request->request->get('minit'));
            $employee->setLastName($request->request->get('lname'));
            $employee->setHireDate(new \DateTime($request->request->get('hire_date')));
            // ilişkiler güncellenmeli

            $em->flush();

            return $this->redirectToRoute('employee_index');
        }

        return $this->render('employee/edit.html.twig', [
            'employee' => $employee,
        ]);
    }

    #[Route('/{emp_id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Employee $employee, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$employee->getEmpId(), $request->request->get('_token'))) {
            $em->remove($employee);
            $em->flush();
        }

        return $this->redirectToRoute('employee_index');
    }
}
