<?php


namespace App\Controller;


use App\Entity\Classe;
use App\Repository\ClasseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/api/classe", name="api_classe_")
 */
class ClasseController extends AbstractController
{
    /**
     * @Route("/list", name="liste", methods={"GET"})
     * @param ClasseRepository $classeRepository
     * @return Response
     */
    public function getAllClasses(ClasseRepository $classeRepository){
        $classes = $classeRepository->findAll();
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($classes, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/list/{id}", name="byId", methods={"GET"})
     * @param Classe $classe
     * @return Response
     */
    public function getClasseById(Classe $classe){
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($classe, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/create", name="create", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function addClasse(Request $request){
        $classe = new Classe();
        $data = json_decode($request->getContent());

        if($data != null) {
            $classe -> setLabelClasse($data -> labelClasse);

            $entityManager = $this -> getDoctrine() -> getManager();

            $entityManager -> persist($classe);
            $entityManager -> flush();

            return new Response('ok', 201);
        }
        return new Response('Product not created', 400);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"PUT"})
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function editClasse(Request $request, $id){
        $entityManager = $this->getDoctrine()->getManager();
        $classe = $entityManager->find(Classe::class, $id);
        $data = json_decode($request->getContent());

        if (!$classe) {
            throw $this->createNotFoundException(
                'There are no articles with the following id: ' . $id
            );
        }

        if($data != null){
            $code = 200;
            $classe->setLabelClasse($data->labelClasse);

            $entityManager->persist($classe);
            $entityManager->flush();

            return new Response('ok', $code);
        }
        return new Response('Classe not updated', 400);
    }

    public function removeClasse($id){
        $entityManager = $this->getDoctrine()->getManager();
        $classeToRemove = $entityManager->find(Classe::class, $id);

        if (!$classeToRemove) {
            throw $this->createNotFoundException(
                'There are no articles with the following id: ' . $id
            );
        }

        $entityManager->remove($classeToRemove);
        $entityManager->flush();
        return new Response('ok');
    }
}