<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Posts;
use Doctrine\Persistence\ManagerRegistry;

class PostsController extends AbstractController
{
    /**
     * @Route("/posts", name="posts_index", methods={"GET"})
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $response = new Response();
        $posts = $doctrine->getRepository(Posts::class)->findAll();
        $data = [];
        foreach ($posts as $post) {
            $data[] = [
                'id' => $post->getId(),
                'title' => $post->getTitle(),
                'description' => $post->getDescription(),
            ];
        }
        $response->setContent(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    /**
     * @Route("/posts/{id}", name="posts_show", methods={"GET"})
     */
    public function show(int $id, ManagerRegistry $doctrine): Response
    {
        $response = new Response();
        $post =  $doctrine->getRepository(Posts::class)->find($id);
        if (!$post) {
            return $this->json('No project found for id ' . $id, 404);
        }
        $data =  [
            'id' => $post->getId(),
            'title' => $post->getTitle(),
            'description' => $post->getDescription(),
        ];
        $response->setContent(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    /**
     * @Route("/posts", name="posts_new", methods={"POST"})
     */
    public function new(Request $request, ManagerRegistry $doctrine): Response
    {
        $response = new Response();
        $entityManager = $doctrine->getManager();
        $post = new Posts();
        $post->setTitle($request->request->get('title'));
        $post->setDescription($request->request->get('description'));
        $entityManager->persist($post);
        $entityManager->flush();
        $response->setContent(json_encode('Created new project successfully with id ' . $post->getId()));
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    /**
     * @Route("/posts", name="posts_delete", methods={"DELETE"})
     */
    public function delete(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $post_data =  $doctrine->getRepository(Posts::class)->findOneBy([], ['id' => 'DESC']);
        $post = $entityManager->getRepository(Posts::class)->findOneBy([], ['id' => 'DESC']);
        if (!$post) {
            return $this->json('No project found for id' . $post_data->getId(), 404);
        }
        $entityManager->remove($post);
        $entityManager->flush();
        return $this->json('Deleted a post successfully with id ' . $post_data->getId());
    }
}
