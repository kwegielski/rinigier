<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\UserFormType;
use App\Form\UserFilterFormType;
use App\Service\UserApi\UserApiInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/users')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserApiInterface $users,
    ) {}

    #[Route('', name: 'user_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $form = $this->createForm(UserFilterFormType::class, null, [
            'method' => 'GET',
        ]);
        $form->handleRequest($request);

        $filters = array_filter($form->getData() ?? []);

        $page = (int) $request->query->get('page', 1);
        $filters['page'] = $page;

        $sort = $request->query->get('sort_column', 'id');
        $direction = $request->query->get('sort_direction', 'asc');
    
        $filters['sort'] = $sort;
        $filters['direction'] = $direction;

        $users = $this->users->list($filters);

        return $this->render('user/index.html.twig', [
            'users' => $users['data'],
            'filter_form' => $form->createView(),
            'sort_column' => $sort,
            'sort_direction' => $direction,
            'current_page' => $page,
        ]);
    }

    #[Route('/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $form = $this->createForm(UserFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->users->create($form->getData());

                $this->addFlash('success', 'User created');

                return $this->redirectToRoute('user_index');
            } catch (ValidationException $ex) {
                foreach ($ex->errors as $field => $messages) {
                    foreach ($messages as $message) {
                        $form->get($field)?->addError(new FormError($message));
                    }
                }
            }
        }

        return $this->render('user/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request): Response
    {
        $user = $this->users->get($id);

        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->users->update($id, $form->getData());
                
                $this->addFlash('success', 'User updated');

                return $this->redirectToRoute('user_index');
            } catch (ValidationException $ex) {
                foreach ($ex->errors as $field => $messages) {
                    foreach ($messages as $message) {
                        $form->get($field)?->addError(new FormError($message));
                    }
                }
            }

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'user_delete', methods: ['POST'])]
    public function delete(int $id): Response
    {
        $this->users->delete($id);
        $this->addFlash('success', 'User deleted');

        return $this->redirectToRoute('user_index');
    }

    #[Route('/import', name: 'user_import', methods: ['POST'])]
    public function import(): Response
    {
       
        try {
            $this->users->import(); 
            
            $this->addFlash('success', 'Users has been imported.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Faild to import users: ' . $e->getMessage());
        }

        return $this->redirectToRoute('user_index');
    }    
}
