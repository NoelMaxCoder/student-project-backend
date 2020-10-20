<?php


namespace App\Controller;


use App\Entity\Role;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


/**
 * @Route("/api/user", name="api_user_")
 */
class UserController extends AbstractController
{
    /** @var UserPasswordEncoderInterface encoderInterface $encoder */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @Route("/list", name="liste", methods={"GET"})
     * @param UserRepository $userRepository
     * @return Response
     */
    public function getAllUser(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();


        $encoders = [new JsonEncoder()];


        $normalizers = [new ObjectNormalizer()];


        $serializer = new Serializer($normalizers, $encoders);


        $jsonContent = $serializer->serialize($users, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        $response = new Response($jsonContent);


        $response->headers->set('Content-Type', 'application/json');


        return $response;
    }

    /**
     * @Route("/list/{id}", name="user", methods={"GET"})
     * @param User $user
     * @return Response
     */
    public function getUserById(User $user)
    {
        $encoders = [new JsonEncoder()];

        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($user, 'json', [
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
    public function addUser(Request $request)
    {
        $user = new User();

        $content = $request->getContent();
        $data = json_decode($content);

        if($data != null){
            $user->setEmail($data->email);
            $user->setUsername($data->username);
            $user->setName($data->name);
            $user->setFirstName($data->firstName);
            $user->setPassword($this->encoder->encodePassword($user, $data->password));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return new Response('ok', 201);
        }
        return new Response('User not created', 400);
    }

    /**
     * @Route("/edit/{id}", name="edit_user", methods={"PUT"})
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function editUser(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->find(User::class, $id);
        $data = json_decode($request->getContent());

        if (!$user) {
            throw $this->createNotFoundException(
                'There are no articles with the following id: ' . $id
            );
        }

        if($data != null){
            $code = 200;
            $user->setEmail($data->email);
            $user->setUsername($data->username);
            $user->setName($data->name);
            $user->setFirstName($data->firstName);

            $entityManager->persist($user);
            $entityManager->flush();

            return new Response('ok', $code);
        }
        return new Response('User not updated', 400);
    }

    /**
     * @Route("/delete/{id}", name="user_delete", methods={"DELETE"})
     * @param $id
     * @return Response
     */
    public function removeUser($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $userToRemove = $entityManager->find(User::class, $id);

        if (!$userToRemove) {
            throw $this->createNotFoundException(
                'There are no articles with the following id: ' . $id
            );
        }
        $entityManager->remove($userToRemove);
        $entityManager->flush();
        return new Response('ok');
    }


}