<?php

namespace App\Controller;

use App\Repository\FoodRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(FoodRepository $repository): Response
    {
        $aliments = $repository->findBy([], ['expiryDate' => 'ASC']);

        return $this->render('home/index.html.twig', [
            'aliments' => $aliments,
        ]);
    }
}
