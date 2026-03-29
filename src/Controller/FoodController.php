<?php

namespace App\Controller;

use App\Entity\Food;
use App\Form\FoodType;
use App\Repository\FoodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

final class FoodController extends AbstractController
{
    #[Route('/food', name: 'app_food')]
    public function index(FoodRepository $foodRepository, #[MapQueryParameter] ?string $search): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $foods = $foodRepository->getAllFromUser($user, $search);

        return $this->render('food/index.html.twig', [
            'foods' => $foods,
        ]);
    }

    #[Route('/food/create', name: 'app_food_create')]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

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

    #[Route('/food/{id}/delete', name: 'app_food_delete', requirements: ['id' => '\d+'])]
    public function delete(Food $food, EntityManagerInterface $entityManager, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($food->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createFormBuilder()
            ->add('delete', SubmitType::class)
            ->add('cancel', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('delete')->isClicked()) {
                $entityManager->remove($food);
                $entityManager->flush();

                return $this->redirectToRoute('app_food');
            }

            return $this->redirectToRoute('app_food');
        }

        return $this->render('food/delete.html.twig',
            ['form' => $form->createView(),
                'food' => $food]
        );
    }
}
