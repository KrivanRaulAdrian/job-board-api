<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{
    use JsonResponseFormat;

    #[Route(path: "/api/auth/register", methods: ["POST"])]
    #[OA\Tag(name: 'auth')]
    #[OA\Post(description: "Create a user")]
    #[OA\RequestBody(
        description: "Json to create the user",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "email", type: "string", example: "example@email.com"),
                new OA\Property(property: "password", type: "string", example: "testpassword"),
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Returns the email of the user',
        content: new OA\JsonContent(ref: new Model(type: ResponseDto::class))
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid inputs',
        content: new OA\JsonContent(ref: new Model(type: ResponseDto::class))
    )]
    public function register(
        Request $request,
        UserRepository $userRepository,
        ValidatorInterface $validatorInterface,
        UserPasswordHasherInterface $hasher
    ): JsonResponse {
        $params = json_decode($request->getContent(), true);

        $user = new User();

        if ($params['password'] === '') {
            return $this->jsonResponse('Password cannot be empty', ['password' => $params['password']], 400);
        } elseif (strlen($params['password']) < 10) {
            return $this->jsonResponse(
                'Password length needs to be at least 10 characters or longer',
                ['password' => $params['password']],
                400
            );
        }

        $hashedPassword = $hasher->hashPassword($user, $params['password']);

        $user->setEmail($params['email']);
        $user->setUsername($params['email']);
        $user->setPassword($hashedPassword);

        $violations = $validatorInterface->validate($user);

        if (count($violations)) {
            return $this->JsonResponse('Invalid inputs', $this->getViolationsFromList($violations), 400);
        }

        $userRepository->save($user, true);

        return $this->json((array)new ResponseDto('User created successfully', ['email' => $user->getEmail()]), 201);
    }
}
