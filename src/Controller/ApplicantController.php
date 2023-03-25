<?php

namespace App\Controller;

use RuntimeException;
use App\Entity\User;
use App\Entity\Applicant;
use OpenApi\Attributes as OA;
use App\Repository\JobRepository;
use App\Repository\ApplicantRepository;
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
#[OA\Tag(name: 'applicant')]
class ApplicantController extends AbstractController
{
    use JsonResponseFormat;

    #[Route(path: "/applicants", methods: ["POST"])]
    #[OA\Post(description: "Create applicant and apply to a job")]
    #[OA\RequestBody(
        description: "Json to create the applicant and apply him for a job",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "name", type: "string", example: "My name example"),
                new OA\Property(property: "contactInformation", type: "string", example: "My contact information example"),
                new OA\Property(property: "jobPreferences", type: "string", example: "My job preferences example"),
                new OA\Property(property: "jobsApplied", type: "string", example: "01870e85-f364-7680-bb24-42762f5afcda"),
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Returns the ID of the applicant',
        content: new OA\JsonContent(ref: new Model(type: ResponseDto::class))
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid inputs',
        content: new OA\JsonContent(ref: new Model(type: ResponseDto::class))
    )]
    public function create(ApplicantRepository $applicantRepository, JobRepository $jobRepository, Request $request, ValidatorInterface $validatorInterface): Response
    {
        $jsonParams = json_decode($request->getContent(), true);

        if ($jsonParams['jobsApplied'] === '') {
            return $this->jsonResponse('Invalid inputs', ['jobsApplied' => $jsonParams['jobsApplied'] . 'This value should not be blank.'], 400);
        }

        $applicant = new Applicant();
        $applicant->setName($jsonParams['name']);
        $applicant->setContactInformation($jsonParams['contactInformation']);
        $applicant->setJobPreferences($jsonParams['jobPreferences']);
        $job = $jobRepository->find($jsonParams['jobsApplied']);
        $applicant->addJobsApplied($job);

        $violations = $validatorInterface->validate($applicant);

        if (count($violations)) {
            return $this->jsonResponse('Invalid inputs', $this->getViolationsFromList($violations), 400);
        }

        $applicantRepository->save($applicant, true);

        $data = ['id' => (string)$applicant->getId()];

        return $this->jsonResponse('Applicant created successfully', $data, 201);
    }

    #[Route(path: "/applicants", methods: ["GET"])]
    #[OA\Get(description: "Return all applicants")]
    public function findAll(ApplicantRepository $applicantRepository, SerializerInterface $serializerInterface, TokenStorageInterface $tokenStorageInterface): Response
    {
        $token = $tokenStorageInterface->getToken();
        $user = $token?->getUser();

        if (!$user instanceof User) {
            throw new RuntimeException('Invalid user from token');
        }

        $applicants = $applicantRepository->findAll();

        $json = $serializerInterface->serialize($applicants, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['jobsApplied']]);
        ;

        return $this->jsonResponse('List of applicants requested by ' . $user->getEmail(), $json);
    }

    #[Route(path: "/applicants/{id}", methods: ["GET"])]
    #[OA\Get(description: "Return an applicant by its ID")]
    public function findById(ApplicantRepository $applicantRepository, string $id, SerializerInterface $serializerInterface): Response
    {
        $applicant = $applicantRepository->find($id);

        if ($applicant === null) {
            return $this->jsonResponse('Applicant not found', ['id' => $id], 404);
        }

        $json = $serializerInterface->serialize($applicant, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['jobsApplied']]);
        ;

        return $this->jsonResponse('Applicant by ID', $json);
    }

    #[Route(path: "/applicants/{id}", methods: ["PUT"])]
    #[OA\Put(description: "Update an applicant by its ID")]
    #[OA\RequestBody(
        description: "Json to update the applicant",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "name", type: "string", example: "My update name example"),
                new OA\Property(property: "contactInformation", type: "string", example: "My update contact information example"),
                new OA\Property(property: "jobPreferences", type: "string", example: "My update job preferences example"),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns the ID of the applicant',
        content: new OA\JsonContent(ref: new Model(type: ResponseDto::class))
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid inputs',
        content: new OA\JsonContent(ref: new Model(type: ResponseDto::class))
    )]
    public function update(ApplicantRepository $applicantRepository, Request $request, ValidatorInterface $validatorInterface, string $id, SerializerInterface $serializerInterface): Response
    {
        $applicant = $applicantRepository->find($id);

        if ($applicant === null) {
            return $this->jsonResponse('Applicant not found', ['id' => $id], 404);
        }

        $jsonParams = json_decode($request->getContent(), true);

        $applicant->setName($jsonParams['name']);
        $applicant->setContactInformation($jsonParams['contactInformation']);
        $applicant->setJobPreferences($jsonParams['jobPreferences']);

        $violations = $validatorInterface->validate($applicant);

        if (count($violations)) {
            return $this->jsonResponse('Invalid inputs', $this->getViolationsFromList($violations), 400);
        }

        $applicantRepository->save($applicant, true);

        $json = $serializerInterface->serialize($applicant, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['jobsApplied']]);
        ;

        return $this->jsonResponse('Applicant updated successfully', $json);
    }

    #[Route(path: "/applicants/{id}", methods: ["DELETE"])]
    #[OA\Delete(description: "Delete an applicant by its ID")]
    public function delete(ApplicantRepository $applicantRepository, string $id): Response
    {
        $applicant = $applicantRepository->find($id);

        if ($applicant === null) {
            return $this->jsonResponse('Applicant not found', ['id' => $id], 404);
        }

        $applicantRepository->remove($applicant, true);

        return $this->jsonResponse('Applicant removed successfully', []);
    }
}
