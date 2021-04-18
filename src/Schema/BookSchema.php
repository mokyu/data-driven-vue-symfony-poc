<?php


namespace App\Schema;

use App\Annotation\Table;
use App\Annotation\Form;
use App\Annotation\Common;
use App\Entity\Book;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Common(
 *     tableTitle="Boekenlijst",
 *     formTitle="Boek aanmaken/bewerken",
 *     list="/api/books",
 *     post="/api/books",
 *     delete="/api/books"
 * )
 */
class BookSchema
{
    public $id;

    /**
     * @Table(
     *     name="ISBN nummer",
     *     path="isbn_number",
     *     fieldType="TextField"
     * )
     * @Form(
     *     rules={"REQUIRED", "LIMIT_LENGTH;255"},
     *     name="ISBN nummer",
     *     fieldType="TextField",
     *     placeholder="ISBN nummer",
     *     path="isbn_number"
     * )
     */
    public $isbn_number;

    /**
     * @Table(
     *     name="Titel",
     *     path="title",
     *     fieldType="TextField"
     * )
     * @Form(
     *     rules={"REQUIRED", "LIMIT_LENGTH;255"},
     *     name="Titel",
     *     fieldType="TextField",
     *     placeholder="Titel",
     *     path="title"
     * )
     */
    public $title;

    /**
     * @Table(
     *     name="Omschrijving",
     *     path="description",
     *     fieldType="TextArea"
     * )
     * @Form(
     *     rules={},
     *     name="Omschrijving",
     *     fieldType="TextArea",
     *     placeholder="Omschrijving van het boek",
     *     path="description"
     * )
     */
    public $description;

    /**
     * @Table(
     *     name="Auteur",
     *     path="author",
     *     fieldType="TextField"
     * )
     * @Form(
     *     rules={"REQUIRED", "LIMIT_LENGTH;255"},
     *     name="Auteur",
     *     fieldType="TextField",
     *     placeholder="Auteur van het boek",
     *     path="author"
     * )
     */
    public $author;

    /**
     * @Table(
     *     name="Uitgebracht op",
     *     path="published_at",
     *     fieldType="DateField"
     * )
     * @Form(
     *     rules={"REQUIRED"},
     *     name="Datum eerste uitgave",
     *     fieldType="DateField",
     *     placeholder="Uitgebracht op",
     *     path="published_at"
     * )
     */
    public $published_at;

    /**
     * @Table(
     *     name="Genre",
     *     path="genre",
     *     fieldType="EnumField",
     *     enumName="GenreEnum",
     *     dataSource="/api/enum/GenreEnum"
     * )
     * @Form(
     *     rules={"REQUIRED"},
     *     name="Genre",
     *     fieldType="EnumField",
     *     placeholder="Genre van het boek",
     *     path="genre",
     *     dataSource="/api/enum/GenreEnum"
     * )
     */
    public $genre;

    /**
     * @Table(
     *     name="Geleend door",
     *     path="borrowed_by",
     *     fieldType="Memberfield"
     * )
     * @Form(
     *     rules={},
     *     name="Geleend door",
     *     fieldType="MemberField",
     *     placeholder="Geleend door",
     *     path="borrowed_by",
     *     dataSource="/api/members"
     * )
     */
    public $borrowed_by;

    public static function MakeFromEntity(?Book $entity): ?self
    {
        if ($entity == null) {
            return null;
        }
        $s = new self();
        $s->id = $entity->getId();
        $s->isbn_number = $entity->getIsbnNumber();
        $s->title = $entity->getTitle();
        $s->author = $entity->getAuthor();
        $s->description = $entity->getDescription();
        $s->published_at = $entity->getPublishedAt()->format("Y-m-d");
        $s->genre = $entity->getGenre();
        $s->borrowed_by = MemberSchema::MakeFromEntity($entity->getBorrowedBy());
        return $s;
    }

    /**
     * This should only be used for creating a new schema
     * @param Request $req
     * @return BookSchema
     */
    public static function MakeFromRequest(Request $req): ?self
    {
        if (self::isValid($req)) {

            $s = new self();
            $s->id = $req->get('id');
            $s->isbn_number = $req->get('isbn_number');
            $s->title = $req->get('title');
            $s->description = $req->get('description');
            $s->author = $req->get('author');
            $s->genre = $req->get('genre');
            $s->borrowed_by = $req->get('borrowed_by');
            return $s;
        }
        return null;
    }

    private static function isValid(Request $req): bool
    {
        $fields = (
            $req->get('isbn_number') !== null &&
            \is_string($req->get('isbn_number')) &&
            \mb_strlen($req->get('isbn_number')) <= 255 &&
            $req->get('title') !== null &&
            \is_string($req->get('title')) &&
            \mb_strlen($req->get('title')) <= 255 &&
            $req->get('author') !== null &&
            \is_string($req->get('author')) &&
            \mb_strlen($req->get('author')) <= 255 &&
            $req->get('genre') !== null &&
            \is_numeric($req->get('genre'))
        );
        if ($fields) {
            try {
                if ($req->get('published_at') !== null) {
                    new \DateTime($req->get('published_at'));
                }
                return true;
            } catch(\Exception $e) {
                return false;
            }
        }
        return false;
    }
}