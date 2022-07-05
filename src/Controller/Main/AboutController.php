<?php

namespace App\Controller\Main;

use App\Repository\AboutRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AboutController extends AbstractController
{
    #[Route('/about', name: 'app_about')]
    public function index(AboutRepository $repository): Response
    {
        return $this->render('main/about/index.html.twig', [
            'about' => $repository->findOneBy(['id' => 3])
        ]);
    }
}
