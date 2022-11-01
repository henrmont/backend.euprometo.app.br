<?php

namespace App\Controller;

use App\Entity\Post;
use App\Service\DataFormat;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class PostController extends AbstractController
{
    #[Route('/create/post', name: 'create_post')]
    public function createPost(ManagerRegistry $doctrine, Request $request, DataFormat $df, SerializerInterface $serializer): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $con = $doctrine->getConnection();
        $request = $df->transformJsonBody($request);

        try {
            $con->beginTransaction();

            $post = new Post();
            $post->setTitle($request->get('title'));
            $post->setSubtitle($request->get('subtitle'));
            $post->setContent($request->get('content'));
            $post->setActive(false);
            $post->setCreatedAt(new \DateTimeImmutable());
            $post->setUpdatedAt(new \DateTimeImmutable());
            $doctrine->getManager()->persist($post);
            $doctrine->getManager()->flush();

            $con->commit();

            $serialized = $serializer->serialize([
                'message'   => 'Post criado com sucesso.',
                'status'    => true
            ],'json');
            return JsonResponse::fromJsonString($serialized);
        } catch (\Exception $e) {
            $con->rollback();
            $serialized = $serializer->serialize([
                'message'   => 'Erro no sistema.',
                'status'    => false
            ],'json');
            return JsonResponse::fromJsonString($serialized);
        }
    }

    #[Route('/get/posts', name: 'get_posts')]
    public function getPosts(ManagerRegistry $doctrine, Request $request, DataFormat $df, SerializerInterface $serializer): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $request = $df->transformJsonBody($request);

        try {
            $posts = $doctrine->getRepository(Post::class)->findAll();

            $serialized = $serializer->serialize([
                'data'      => $posts,
                'status'    => true
            ],'json');
            return JsonResponse::fromJsonString($serialized);
        } catch (\Exception $e) {
            $serialized = $serializer->serialize([
                'message'   => 'Erro no sistema.',
                'status'    => false
            ],'json');
            return JsonResponse::fromJsonString($serialized);
        }
    }
}
