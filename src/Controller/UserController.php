<?php
 
namespace App\Controller;
 
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
 
#[Route('/api', name: 'api_')]
class UserController extends AbstractController
{
    #[Route('/register', name: 'register', methods: 'post')]
    public function index(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $em = $doctrine->getManager();
        $decoded = json_decode($request->getContent());
        $email = $decoded->email;
        $name = $decoded->name;
        $telephone = $decoded->telephone;
        $plaintextPassword = $decoded->password;
   
        $user = new User();
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $user->setEmail($email);
        $user->setName($name);
        $user->setTelephone($telephone);
        $user->setUsername($email);
        $em->persist($user);
        $em->flush();
   
        return $this->json(['message' => 'Registered Successfully']);
    }

    #[Route('/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(): void
    {

        throw new \Exception('This should never be reached!');
    }
}


