<?php

namespace App\Controller;


use App\Entity\Candidature;
use App\Repository\UserRepository;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CandidatureRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CandidatureController extends AbstractController
{
    // #[Route('/test', name: 'test', methods: ['POST'])]
    // public function test(SerializerInterface $serializer,Request $request,Security $security,FormationRepository $form,UserRepository $usersRepo,EntityManagerInterface $em): JsonResponse
    // {
    //     $cand = $serializer->deserialize($request->getContent(),Candidature::class,'json');
    //     $userObjet = $security->getUser();
    //     $data = $request->toArray();
    //     $idFormation = $data['formation']??-1;
    //     $cand->setUser($userObjet);
    //     $cand->setFormation($form->find($idFormation));
    //     $em->persist($cand);
    //     $em->flush();
    //     return new JsonResponse($serializer->serialize($cand, 'json'), JsonResponse::HTTP_OK, [], true);
    // }

    #[Route('/api/teste', name: 'test', methods: ['POST'])]

    public function teste(SerializerInterface $serializer, Request $request, Security $security, FormationRepository $form, UserRepository $usersRepo, EntityManagerInterface $em): JsonResponse
    {
        $cand = $serializer->deserialize($request->getContent(), Candidature::class, 'json');
        $userObjet = $security->getUser();
        $data = $request->toArray();
        $idFormation = $data['formatione'] ?? -1;
        $cand->setUser($userObjet);
        $cand->setFormation($form->find($idFormation));
        $em->persist($cand);
        $em->flush();
        $cands = $serializer->serialize($cand, 'json', ['groups' => 'candidature']);

        return new JsonResponse($cands, Response::HTTP_OK, [], true);
    }


    #[Route('/api/candidature/refuser/{id}', name: 'refused_candidature', methods: ['PUT'])]

    public function refuser(Candidature $candidature, EntityManagerInterface $em, SerializerInterface $sz): JsonResponse
    {
        $candidature->setStatut(true);

        $em->flush();
        return new JsonResponse(['message' => 'Candidature refusée'], JsonResponse::HTTP_OK);
    }

    #[Route('/api/candidatures/accepted', name: 'candidature_accepted', methods: ['GET'])]
    public function getAcceptedCandidatures(CandidatureRepository $candidatureRepository, SerializerInterface $serializer): JsonResponse
    {
        $acceptedCandidatures = $candidatureRepository->findBy(['statut' => true]);

        $data = $serializer->serialize($acceptedCandidatures, 'json', ['groups' => 'candidature']);

        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/api/candidatures/refused', name: 'candidature_refused', methods: ['GET'])]
    public function getRefusedCandidatures(CandidatureRepository $candidatureRepository, SerializerInterface $serializer): JsonResponse
    {
        $acceptedCandidatures = $candidatureRepository->findBy(['statut' => false]);

        $data = $serializer->serialize($acceptedCandidatures, 'json', ['groups' => 'candidature']);

        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }

}
