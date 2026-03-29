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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $limitDate = 3;
        $expiringSoon = $repository->findExpiringSoon($user, $limitDate);
        $expiring = $repository->findExpiring($user);

        return $this->render('home/index.html.twig', [
            'expiringSoon' => $expiringSoon,
            'expiring' => $expiring,
            'limitDate' => $limitDate,
        ]);
    }
}
