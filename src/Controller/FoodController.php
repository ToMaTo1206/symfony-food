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
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class FoodController extends AbstractController
{
    #[Route('/food', name: 'app_food')]
    public function index(FoodRepository $foodRepository, #[MapQueryParameter] ?string $search): Response
    {
        $foods = $foodRepository->getAllFromUser($this->getUser(), $search);

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

            $this->addFlash('success', 'L\'aliment a bien été crée.');
            return $this->redirectToRoute('app_food');
        }

        return $this->render('food/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/food/{food}/update', name: 'food_update', requirements: ['food' => '\d+'])]
    public function update(Request $request, EntityManagerInterface $entityManager, Food $food): Response
    {
        if ($food->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'Cet aliment n\'existe pas.');
            return $this->redirectToRoute('app_food');
        }

        $form = $this->createForm(FoodType::class, $food);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'L\'aliment a bien été modifié.');
            return $this->redirectToRoute('app_food');
        }

        return $this->render('food/update.html.twig', [
            'form' => $form,
            'food' => $food,
        ]);
    }

    #[Route('/food/{food}/delete', name: 'food_delete', requirements: ['food' => '\d+'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, Food $food): Response
    {
        if ($food->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'Cet aliment n\'existe pas.');
            return $this->redirectToRoute('app_food');
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

                $this->addFlash('success', 'L\'aliment a bien été supprimé.');
                return $this->redirectToRoute('app_food');
            }

            return $this->redirectToRoute('app_food');
        }

        return $this->render('food/delete.html.twig', [
            'form' => $form,
            'food' => $food
        ]);
    }
}
