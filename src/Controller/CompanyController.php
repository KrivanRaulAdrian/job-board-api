<?php

namespace App\Controller;

use App\Entity\User;
use RuntimeException;
use App\Entity\Company;
use OpenApi\Attributes as OA;
use App\Repository\CompanyRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route(path: "api/v1")]
#[OA\Tag(name: 'company')]
class CompanyController extends AbstractController
{
    use JsonResponseFormat;

    #[Route(path: "/companies", methods: ["POST"])]
    #[OA\Post(description: "Create company")]
    #[OA\RequestBody(
        description: "Json to create the company",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "name",
                    type: "string",
                    example: "My name example"
                ),
                new OA\Property(
                    property: "description",
                    type: "string",
                    example: "My description example"
                ),
                new OA\Property(
                    property: "location",
                    type: "string",
                    example: "My location example"
                ),
                new OA\Property(
                    property: "contactInformation",
                    type: "string",
                    example: "My contact information example"
                ),
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Returns the ID of the company',
        content: new OA\JsonContent(ref: new Model(type: ResponseDto::class))
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid inputs',
        content: new OA\JsonContent(ref: new Model(type: ResponseDto::class))
    )]
    public function create(
        CompanyRepository $companyRepository,
        Request $request,
        ValidatorInterface $validatorInterface
    ): Response {
        $jsonParams = json_decode($request->getContent(), true);

        $company = new Company();
        $company->setName($jsonParams['name']);
        $company->setDescription($jsonParams['description']);
        $company->setLocation($jsonParams['location']);
        $company->setContactInformation($jsonParams['contactInformation']);

        $violations = $validatorInterface->validate($company);

        if (count($violations)) {
            return $this->jsonResponse('Invalid inputs', $this->getViolationsFromList($violations), 400);
        }
        $companyRepository->save($company, true);

        $data = ['id' => (string)$company->getId()];

        return $this->jsonResponse('Company created successfully', $data, 201);
    }

    #[Route(path: "/companies", methods: ["GET"])]
    #[OA\Get(description: "Return all companies")]
    public function findAll(
        CompanyRepository $companyRepository,
        SerializerInterface $serializerInterface,
        TokenStorageInterface $tokenStorageInterface
    ): Response {
        $token = $tokenStorageInterface->getToken();
        $user = $token?->getUser();

        if (!$user instanceof User) {
            throw new RuntimeException('Invalid user from token');
        }

        $companies = $companyRepository->findAll();

        $json = $serializerInterface->serialize(
            $companies,
            'json',
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['applicants', 'company']]
        );

        return $this->jsonResponse('List of companies requested by ' . $user->getEmail(), $json);
    }

    #[Route(path: "/companies/{id}", methods: ["GET"])]
    #[OA\Get(description: "Return a company by its ID")]
    public function findById(
        CompanyRepository $companyRepository,
        string $id,
        SerializerInterface $serializerInterface
    ): Response {
        $company = $companyRepository->find($id);

        if ($company === null) {
            return $this->jsonResponse('Company not found', ['id' => $id], 404);
        }

        $json = $serializerInterface->serialize(
            $company,
            'json',
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['applicants', 'company']]
        );

        return $this->jsonResponse('Company by ID', $json);
    }

    #[Route(path: "/companies/{id}", methods: ["PUT"])]
    #[OA\Put(description: "Update a company by its ID")]
    #[OA\RequestBody(
        description: "Json to update the company",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "name",
                    type: "string",
                    example: "My update name example"
                ),
                new OA\Property(
                    property: "description",
                    type: "string",
                    example: "My update description example"
                ),
                new OA\Property(
                    property: "location",
                    type: "string",
                    example: "My update location example"
                ),
                new OA\Property(
                    property: "contactInformation",
                    type: "string",
                    example: "My update contact information example"
                ),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns the ID of the company',
        content: new OA\JsonContent(ref: new Model(type: ResponseDto::class))
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid inputs',
        content: new OA\JsonContent(ref: new Model(type: ResponseDto::class))
    )]
    public function update(
        CompanyRepository $companyRepository,
        Request $request,
        ValidatorInterface $validatorInterface,
        string $id,
        SerializerInterface $serializerInterface
    ): Response {
        $company = $companyRepository->find($id);

        if ($company === null) {
            return $this->jsonResponse('Company not found', ['id' => $id], 404);
        }

        $jsonParams = json_decode($request->getContent(), true);

        $company->setName($jsonParams['name']);
        $company->setDescription($jsonParams['description']);
        $company->setLocation($jsonParams['location']);
        $company->setContactInformation($jsonParams['contactInformation']);

        $violations = $validatorInterface->validate($company);

        if (count($violations)) {
            return $this->jsonResponse('Invalid inputs', $this->getViolationsFromList($violations), 400);
        }

        $companyRepository->save($company, true);

        $json = $serializerInterface->serialize(
            $company,
            'json',
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['applicants', 'company']]
        );

        return $this->jsonResponse('Company updated successfully', $json);
    }

    #[Route(path: "/companies/{id}", methods: ["DELETE"])]
    #[OA\Delete(description: "Delete a company by its ID")]
    public function delete(CompanyRepository $companyRepository, string $id): Response
    {
        $company = $companyRepository->find($id);

        if ($company === null) {
            return $this->jsonResponse('Company not found', ['id' => $id], 404);
        }

        $companyRepository->remove($company, true);

        return $this->jsonResponse('Company removed successfully', []);
    }
}
