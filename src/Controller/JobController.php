<?php

namespace App\Controller;

use App\Entity\Job;
use OpenApi\Attributes as OA;
use App\Repository\JobRepository;
use Doctrine\ORM\Query\Expr\Join;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: "api/v1")]
#[OA\Tag(name: 'job')]
class JobController extends AbstractController
{
    use JsonResponseFormat;

    #[Route(path: "/jobs", methods: ["POST"])]
    #[OA\Post(description: "Create job")]
    #[OA\RequestBody(
        description: "Json to create the job",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "title", type: "string", example: "My title example"),
                new OA\Property(property: "description", type: "string", example: "My description example"),
                new OA\Property(property: "requiredSkills", type: "string", example: "My required skills example"),
                new OA\Property(property: "experience", type: "string", example: "My experience example"),
                new OA\Property(property: "company", type: "string", example: "018703c3-1dce-7bc9-a627-39bd25e58d61"),
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Returns the ID of the job',
        content: new OA\JsonContent(ref: new Model(type: ResponseDto::class))
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid inputs',
        content: new OA\JsonContent(ref: new Model(type: ResponseDto::class))
    )]
    public function create(JobRepository $jobRepository, CompanyRepository $companyRepository, Request $request, ValidatorInterface $validatorInterface): Response
    {
        $jsonParams = json_decode($request->getContent(), true);

        $job = new Job();
        $job->setTitle($jsonParams['title']);
        $job->setDescription($jsonParams['description']);
        $job->setRequiredSkills($jsonParams['requiredSkills']);
        $job->setExperience($jsonParams['experience']);
        $company = $companyRepository->find($jsonParams['company']);
        $job->setCompany($company);

        $violations = $validatorInterface->validate($job);

        if (count($violations)) {
            return $this->jsonResponse('Invalid inputs', $this->getViolationsFromList($violations), 400);
        }
        $jobRepository->save($job, true);

        $data = ['id' => (string)$job->getId()];

        return $this->jsonResponse('Job created successfully', $data, 201);
    }
    #[Route(path: "/jobs", methods: ["GET"])]
    #[OA\Get(description: "Return all jobs with optional filters")]
    #[OA\QueryParameter(name: "title", example: "PHP developer")]
    #[OA\QueryParameter(name: "name", example: "Jagaad")]
    #[OA\QueryParameter(name: "location", example: "Italy")]
    public function findAll(EntityManagerInterface $entityManagerInterface, SerializerInterface $serializerInterface, Request $request): Response
    {
        $title = $request->get('title');
        $companyName = $request->get('name');
        $companyLocation = $request->get('location');

        $queryBuilder = $entityManagerInterface
            ->getRepository(Job::class)
            ->createQueryBuilder('j')
            ->leftJoin('j.company', 'c', Join::ON);

        if ($title !== null) {
            $queryBuilder->andWhere('j.title LIKE :title')
                ->setParameter(':title', "%$title%");
        }

        if ($companyName !== null) {
            $queryBuilder->andWhere('c.name LIKE :name')
                ->setParameter(':name', "%$companyName%");
        }

        if ($companyLocation !== null) {
            $queryBuilder->andWhere('c.location LIKE :location')
                ->setParameter(':location', "%$companyLocation%");
        }

        $jobs = $queryBuilder->getQuery()->execute();

        $json = $serializerInterface->serialize($jobs, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['jobPosts', '__isCloning', 'jobsApplied']]);
        ;

        return $this->jsonResponse('List of jobs requested', $json);
    }
    #[Route(path: "/jobs/{id}", methods: ["GET"])]
    #[OA\Get(description: "Return a job by its ID")]
    public function findById(JobRepository $jobRepository, string $id, SerializerInterface $serializerInterface): Response
    {
        $job = $jobRepository->find($id);

        if ($job === null) {
            return $this->jsonResponse('Job not found', ['id' => $id], 404);
        }

        $json = $serializerInterface->serialize($job, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['jobPosts', '__isCloning', 'jobsApplied']]);
        ;

        return $this->jsonResponse('Job by ID', $json);
    }
    #[Route(path: "/jobs/{id}", methods: ["PUT"])]
    #[OA\Put(description: "Update a job by its ID")]
    #[OA\RequestBody(
        description: "Json to update the job",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "title", type: "string", example: "My update title example"),
                new OA\Property(property: "description", type: "string", example: "My update description example"),
                new OA\Property(property: "requiredSkills", type: "string", example: "My update required skills example"),
                new OA\Property(property: "experience", type: "string", example: "My update experience example"),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns the ID of the job',
        content: new OA\JsonContent(ref: new Model(type: ResponseDto::class))
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid inputs',
        content: new OA\JsonContent(ref: new Model(type: ResponseDto::class))
    )]
    public function update(JobRepository $jobRepository, Request $request, ValidatorInterface $validatorInterface, string $id, SerializerInterface $serializerInterface): Response
    {
        $job = $jobRepository->find($id);

        if ($job === null) {
            return $this->jsonResponse('Job not found', ['id' => $id], 404);
        }

        $jsonParams = json_decode($request->getContent(), true);

        $job->setTitle($jsonParams['title']);
        $job->setDescription($jsonParams['description']);
        $job->setRequiredSkills($jsonParams['requiredSkills']);
        $job->setExperience($jsonParams['experience']);

        $violations = $validatorInterface->validate($job);

        if (count($violations)) {
            return $this->jsonResponse('Invalid inputs', $this->getViolationsFromList($violations), 400);
        }

        $jobRepository->save($job, true);

        $json = $serializerInterface->serialize($job, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['jobPosts', '__isCloning', 'jobsApplied']]);
        ;

        return $this->jsonResponse('Job updated successfully', $json);
    }
    #[Route(path: "/jobs/{id}", methods: ["DELETE"])]
    #[OA\Delete(description: "Delete a job by its ID")]
    public function delete(JobRepository $jobRepository, string $id): Response
    {
        $job = $jobRepository->find($id);

        if ($job === null) {
            return $this->jsonResponse('Job not found', ['id' => $id], 404);
        }

        $jobRepository->remove($job, true);

        return $this->jsonResponse('Job removed successfully', []);
    }
}
