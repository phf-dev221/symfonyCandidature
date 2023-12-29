<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormationController extends AbstractController
{
    #[Route('/api/formation/create', name: 'create_formation', methods: ['POST'])]
    public function register(EntityManagerInterface $em, Request $request, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        $formation = $serializer->deserialize($request->getContent(), Formation::class, 'json');

        $errors = $validator->validate($formation);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }else {
            $em->persist($formation);
            $em->flush();
            return new JsonResponse($serializer->serialize($formation, 'json'), JsonResponse::HTTP_CREATED, [], true);
        }
    }

#[Route('/api/formation/index', name:"allFormation", methods: ['GET'])]
public function index(FormationRepository $formationRepository, SerializerInterface $serializer, ):JsonResponse
{
    $formations = $formationRepository->findAll();
}

}