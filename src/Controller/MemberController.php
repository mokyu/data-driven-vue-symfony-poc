<?php

namespace App\Controller;

use App\Entity\Member;
use App\Repository\MemberRepository;
use App\Schema\MemberSchema;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MemberController extends AbstractController
{
    /**
     * @Route("/api/members", name="member")
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
}
