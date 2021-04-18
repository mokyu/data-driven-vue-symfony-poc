<?php


namespace App\Schema;

use App\Annotation\Table;
use App\Annotation\Form;
use App\Annotation\Common;
use App\Entity\Member;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Common(
 *     tableTitle="Ledenlijst",
 *     formTitle="Lid aanmaken/bewerken",
 *     list="/api/members",
 *     post="/api/members",
 *     delete="/api/members"
 * )
 */
class MemberSchema
{
    public $id;

    /**
     * @Table(
     *     name="Naam",
     *     path="name",
     *     fieldType="TextField"
     * )
     * @Form(
     *     rules={"REQUIRED", "LIMIT_LENGTH;255"},
     *     name="Naam",
     *     fieldType="TextField",
     *     placeholder="Naam van lid",
     *     path="name"
     * )
     */
    public $name;

    public static function MakeFromEntity(?Member $entity): ?self
    {
        if ($entity == null) {
            return null;
        }
        $s = new self();
        $s->name = $entity->getName();
        $s->id = $entity->getId();
        return $s;
    }

    /**
     * This should only be used for creating a new schema
     * @param Request $req
     * @return MemberSchema
     */
    public static function MakeFromRequest(Request $req): ?self
    {
        if (self::isValid($req)) {

            $s = new self();
            $s->name = $req->get('name');
            $s->id = $req->get('id');
            return $s;
        }
        return null;
    }

    private static function isValid(Request $req): bool
    {
        return (
            $req->get('name') !== null &&
            \is_string($req->get('name')) &&
            \mb_strlen($req->get('name')) <= 255
        );
    }
}