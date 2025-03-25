<?php

namespace App\Controller;

use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    public function __construct(
        private readonly EmailService $emailService
    ) {}

    #[Route('/test', name: 'app_test_index')]
    public function index(): Response
    {
        $template = $this->render('email/template.html.twig', [
            'subject' => 'Sujet du message',
            'body' => 'Contenu du message'
        ]);
        $this->emailService->sendEmail('florian.nickels@gmail.com', 'Test', $template->getContent());
        return new Response('Email sent');
    }
}
