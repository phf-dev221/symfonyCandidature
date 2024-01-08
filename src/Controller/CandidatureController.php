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
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CandidatureController extends AbstractController
{

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

        $data = [];
        $data[] = [
            "id" => $cand->getId(),
            "libellé" => $cand->getFormation()->getLibelle(),
            "nom user" => $cand->getUser()->getName(),
            " Email" => $cand->getUser()->getEmail(),

        ];
        return new JsonResponse($serializer->serialize($data, 'json'), JsonResponse::HTTP_CREATED, [], true);

    }

    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour effectuer cette action')]

    #[Route('/api/candidature/refuser/{id}', name: 'refused_candidature', methods: ['PUT'])]

    public function refuser(Candidature $candidature, EntityManagerInterface $em, SerializerInterface $sz): JsonResponse
    {
        $candidature->setStatut(true);

        $em->flush();
        return new JsonResponse(['message' => 'Candidature refusée'], JsonResponse::HTTP_OK);
    }

    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour effectuer cette action')]
    #[Route('/api/candidatures/accepted', name: 'candidature_accepted', methods: ['GET'])]
    public function getAcceptedCandidatures(CandidatureRepository $candidatureRepository, SerializerInterface $serializer): JsonResponse
    {
        $acceptedCandidatures = $candidatureRepository->findBy(['statut' => true]);

        $data = [];
        foreach ($acceptedCandidatures as $candidature) {
            $data[] = [
                'id' => $candidature->getId(),
                'user' => [
                    'username' => $candidature->getUser()->getName(),
                ],
                'formation' => [
                    'libelle' => $candidature->getFormation()->getLibelle(),
                ],
                'statut' => $candidature->isStatut(),
            ];
        }
        return new JsonResponse($serializer->serialize($data, 'json'), JsonResponse::HTTP_OK, [], true);
    }

    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour effectuer cette action')]
    #[Route('/api/candidatures/refused', name: 'candidature_refused', methods: ['GET'])]
    public function getRefusedCandidatures(CandidatureRepository $candidatureRepository, SerializerInterface $serializer): JsonResponse
    {
        $acceptedCandidatures = $candidatureRepository->findBy(['statut' => false]);
        $data = [];
        foreach ($acceptedCandidatures as $candidature) {
            $data[] = [
                'id' => $candidature->getId(),
                'user' => [
                    'username' => $candidature->getUser()->getName(),
                ],
                'formation' => [
                    'libelle' => $candidature->getFormation()->getLibelle(),
                ],
                'statut' => $candidature->isStatut(),
            ];
        }
        return new JsonResponse($serializer->serialize($data, 'json'), JsonResponse::HTTP_OK, [], true);
    }

}
    