<?php


namespace App\Controller;


use App\Entity\Classe;
use App\Entity\Student;
use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/api/student", name="api_student_" )
 */
class StudentController extends AbstractController
{
    /**
     * @Route("/list", name="liste", methods={"GET"})
     * @param StudentRepository $studentRepository
     * @return Response
     */
    public function getAllStudents(StudentRepository $studentRepository){
        $students = $studentRepository->findAll();
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($students, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/list/{id}", name="ById", methods={"GET"})
     * @param Student $student
     * @return Response
     */
    public function getStudentById(Student $student){
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($student, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/create/classe/{id}", name="add_to_classe", methods={"POST"})
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function addStudentToClasse(Request $request, $id){
        $student = new Student();
        $entityManager = $this->getDoctrine()->getManager();
        $classe = $entityManager->find(Student::class, $id);

        $data = json_decode($request->getContent());

        if($data != null) {
            $student->setFirstName($data->firstName);
            $student->setName($data->name);
            $student->setCourse($data->course);
            $student->setStudentClasse($classe);

            $entityManager = $this -> getDoctrine() -> getManager();

            $entityManager -> persist($student);
            $entityManager -> flush();

            return new Response('ok', 201);
        }
        return new Response('Student not created', 400);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"PUT"})
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function editStudent(Request $request, $id){
        $entityManager = $this->getDoctrine()->getManager();
        $student = $entityManager->find(Student::class, $id);

        $data = json_decode($request->getContent());

        if (!$student) {
            throw $this->createNotFoundException(
                'There are no articles with the following id: ' . $id
            );
        }

        if($data != null){
            $code = 200;
            $student->setfirstName($data->firstName);
            $student->setName($data->name);
            $student->setCourse($data->course);

            $entityManager->persist($student);
            $entityManager->flush();

            return new Response('ok', $code);
        }
        return new Response('Student not updated', 400);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"DELETE"})
     * @param $id
     * @return Response
     */
    public function removeStudent($id){
        $entityManager = $this->getDoctrine()->getManager();
        $studentToRemove = $entityManager->find(Student::class, $id);

        if (!$studentToRemove) {
            throw $this->createNotFoundException(
                'There are no articles with the following id: ' . $id
            );
        }

        $entityManager->remove($studentToRemove);
        $entityManager->flush();
        return new Response('ok');
    }
}