<?php

namespace App\Controller;

use App\Entity\Politician;
use App\Service\DataFormat;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PoliticianController extends AbstractController
{
    #[Route('/api/create/politician', name: 'create_politician')]
    public function createPolitician(ManagerRegistry $doctrine, Request $request, DataFormat $df, SerializerInterface $serializer): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $con = $doctrine->getConnection();
        $request = $df->transformJsonBody($request);

        try {
            $con->beginTransaction();

            $politician = new Politician();
            $politician->setName($request->get('name'));
            $politician->setParty($request->get('party'));
            $politician->setState($request->get('state'));
            $politician->setType($request->get('type'));
            if ($request->get('image')) {
                $politician->setImage($request->get('image'));
            }
            $politician->setActive(false);
            $politician->setCreatedAt(new \DateTimeImmutable());
            $politician->setUpdatedAt(new \DateTimeImmutable());
            $doctrine->getManager()->persist($politician);

            $doctrine->getManager()->flush();

            $con->commit();

            $serialized = $serializer->serialize([
                'message'   => 'Politico criado com sucesso.',
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

    #[Route('/get/politicians', name: 'get_politicians')]
    public function getPoliticians(ManagerRegistry $doctrine, Request $request, DataFormat $df, SerializerInterface $serializer): Response
    {
        try {
            $politicians = $doctrine->getRepository(Politician::class)->findAll();

            $serialized = $serializer->serialize([
                'data'      => $politicians,
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

    #[Route('/get/politician/{id}', name: 'get_politician')]
    public function getPolitician(ManagerRegistry $doctrine, Request $request, DataFormat $df, SerializerInterface $serializer, $id): Response
    {
        try {
            $politician = $doctrine->getRepository(Politician::class)->find($id);

            $serialized = $serializer->serialize([
                'data'      => $politician,
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

    #[Route('/api/edit/politician', name: 'edit_politician')]
    public function editPolitician(ManagerRegistry $doctrine, Request $request, DataFormat $df, SerializerInterface $serializer): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $con = $doctrine->getConnection();
        $request = $df->transformJsonBody($request);

        try {
            $con->beginTransaction();

            $politician = $doctrine->getRepository(Politician::class)->find($request->get('id'));
            $politician->setName($request->get('name'));
            $politician->setParty($request->get('party'));
            $politician->setState($request->get('state'));
            $politician->setType($request->get('type'));
            if ($request->get('image')) {
                $politician->setImage($request->get('image'));
            }
            $politician->setUpdatedAt(new \DateTimeImmutable());
            $doctrine->getManager()->persist($politician);

            $doctrine->getManager()->flush();

            $con->commit();

            $serialized = $serializer->serialize([
                'message'   => 'Politico editado com sucesso.',
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

    #[Route('/api/status/politician', name: 'status_politician')]
    public function statusPolitician(ManagerRegistry $doctrine, Request $request, DataFormat $df, SerializerInterface $serializer): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $con = $doctrine->getConnection();
        $request = $df->transformJsonBody($request);

        try {
            $con->beginTransaction();

            $politician = $doctrine->getRepository(Politician::class)->find($request->get('id'));
            if ($politician->isActive()) {
                $politician->setActive(false);
            } else {
                $politician->setActive(true);
            }
            $politician->setUpdatedAt(new \DateTimeImmutable());
            $doctrine->getManager()->persist($politician);

            $doctrine->getManager()->flush();

            $con->commit();

            $serialized = $serializer->serialize([
                'message'   => 'Status alterado com sucesso.',
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

    #[Route('/api/delete/politician', name: 'delete_politician')]
    public function deletePolitician(ManagerRegistry $doctrine, Request $request, DataFormat $df, SerializerInterface $serializer): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $con = $doctrine->getConnection();
        $request = $df->transformJsonBody($request);

        try {
            $con->beginTransaction();

            $politician = $doctrine->getRepository(Politician::class)->find($request->get('id'));
            $doctrine->getManager()->remove($politician);

            $doctrine->getManager()->flush();

            $con->commit();

            $serialized = $serializer->serialize([
                'message'   => 'Político deletado com sucesso.',
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
}
