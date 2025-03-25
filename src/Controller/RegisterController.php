<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use  App\Form\RegisterType;
use App\Entity\Account;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RegisterController extends AbstractController
{

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly EntityManagerInterface $em,
        private readonly ValidatorInterface $validator,
        private readonly UserPasswordHasherInterface $hasher,
    ) {}
    #[Route('/register', name: 'app_register_addaccount')]
    public function addAccount(Request $request): Response
    {
        $account = new Account();
        $form = $this->createForm(RegisterType::class, $account);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $request->request->all("register")["password"]["first"];
            $account->setPassword($this->hasher->hashPassword($account, $password));
            $errors = $this->validator->validate($account);
            if (count($errors) > 0) {
                $msg = $errors[0]->getMessage();
                $type = "warning";
            } else {
                $account->setRoles(['ROLE_USER']);
                $account->setStatus(false);
                if (!$this->accountRepository->findOneBy(['email' => $account->getEmail()])) {
                    $account->setRoles(['ROLE_USER']);
                    $this->em->persist($account);
                    $this->em->flush();
                    $msg = "Account created successfully";
                    $type = "success";
                } else {
                    $msg = "Account already exists";
                    $type = "danger";
                }
            }
            $this->addFlash($type, $msg);
        }
        return $this->render('register/index.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/register/{id}', name: 'app_register_activate')]
    public function activate(mixed $id): void
    {
        if (is_numeric($id)) {
            $this->accountRepository->findOneBy(['id' => $id])->setStatus(true);
        } else {
            echo "Invalid id";
        }
    }
}
