<?php

namespace App\Controller;

use App\Entity\Member;
use App\Repository\MemberRepository;
use App\Schema\MemberSchema;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MemberController extends AbstractController
{
    /**
     * @Route("/api/members", name="member", methods={"GET"})
     */
    public function listMembers(): JsonResponse
    {
        /** @var MemberRepository $rep */
        $rep = $this->getDoctrine()->getRepository(Member::class);
        $result = $rep->findAll();
        return new JsonResponse(array_map('self::cb', $result));
    }

    private static function cb($data) : MemberSchema
    {
        return MemberSchema::MakeFromEntity($data);
    }

    /**
     * Create/Update a book
     * @Route("/api/members", name="member_schema_post", methods={"POST"})
     * @param Request $req
     * @return JsonResponse
     */
    public function saveMember(Request $req): JsonResponse
    {
        $schema = MemberSchema::MakeFromRequest($req);
        $em = $this->getDoctrine()->getManager();

        if ($schema && $schema->id === null) {
            $member = new Member();
            $member->setFromSchema($schema);
            $em->persist($member);
            $em->flush();
            return new JsonResponse(MemberSchema::MakeFromEntity($member));
        } else if ($schema && $schema->id !== null) {
            /** @var MemberRepository $mrep */
            $mrep = $em->getRepository(Member::class);
            $criteria = array('id' => $schema->id);
            $result = $mrep->findOneBy($criteria);
            if ($result !== null) {
                $result->setFromSchema($schema);
                $em->persist($result);
                $em->flush();
                return new JsonResponse(MemberSchema::MakeFromEntity($result));
            } else {
                return new JsonResponse(['error' => 'Failed to update entity']);
            }
        } else {
            return new JsonResponse(['error' => 'Failed to create entity']);
        }
    }

    /**
     * Delete a book
     * @Route("/api/members", name="member_schema_delete", methods={"DELETE"})
     * @param Request $req
     * @return JsonResponse
     */
    public function deleteBook(Request $req): JsonResponse
    {
        $schema = MemberSchema::MakeFromRequest($req);
        $em = $this->getDoctrine()->getManager();

        if ($schema && $schema->id !== null) {
            /** @var MemberRepository $rep */
            $rep = $this->getDoctrine()->getRepository(Member::class);
            $criteria = array('id' => $schema->id);
            $result = $rep->findOneBy($criteria);
            if ($result) {
                $em->remove($result);
                $em->flush();
                return new JsonResponse(['success' => 'Entity deleted']);
            } else {
                return new JsonResponse(['error' => 'entity with given id does not exist']);
            }
        } else {
            return new JsonResponse(['error' => 'Failed to delete entity']);
        }
    }
}
