<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Post;

class PostController extends AbstractController
{
    /**
     * @Route("/post", name="project_index", methods={"GET"})
     */
    public function index(): Response
    {
        $posts = $this->getDoctrine()->getRepository(Post::class)->findAll();
        $data = [];
        foreach ($posts as $post) {
            $data[] = [
                'id' => $post->getId(),
                'title' => $post->getTitle(),
                'description' => $post->getDescription(),
            ];
        }
        return $this->json($data);
    }

    /**
     * @Route("/post", name="project_new", methods={"POST"})
     */
    public function new(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $post = new Post();
        $post->setTitle($request->request->get('title'));
        $post->setDescription($request->request->get('description'));
        $entityManager->persist($post);
        $entityManager->flush();
        return $this->json('Created new project successfully with id ' . $post->getId());
    }

    /**
     * @Route("/project/{id}", name="project_delete", methods={"DELETE"})
     */
    public function delete(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $post = $entityManager->getRepository(Post::class)->find($id);
        if (!$post) {
            return $this->json('No project found for id' . $id, 404);
        }
        $entityManager->remove($post);
        $entityManager->flush();
        return $this->json('Deleted a post successfully with id ' . $id);
    }
}
