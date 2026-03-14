<?php

namespace App\Controller;

use App\Entity\Food;
use App\Form\FoodType;
use App\Repository\FoodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FoodController extends AbstractController
{
    #[Route('/food', name: 'app_food')]
    public function index(FoodRepository $foodRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $foods = $foodRepository->getAllFromUser($user);

        return $this->render('food/index.html.twig', [
            'foods' => $foods,
        ]);
    }

    #[Route('/food/create', name: 'app_food_create')]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $food = new Food();
        $food->setUser($user);

        $form = $this->createForm(FoodType::class, $food);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($food);
            $entityManager->flush();
            return $this->redirectToRoute('app_food');
        }

        return $this->render('food/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    #[Route('/food/{id}/update', name: 'app_food_update', requirements: ['id' => '\d+'])]
    public function update(Request $request, Food $food, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($food->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Cet aliment ne vous appartient pas !');
        }

        $form = $this->createForm(FoodType::class, $food);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_food');
        }

        return $this->render(
            'food/update.html.twig',
        ['form' => $form]
        );
    }

}
