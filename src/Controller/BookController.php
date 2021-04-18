<?php


namespace App\Controller;

use App\Entity\Book;
use App\Entity\Member;
use App\Repository\BookRepository;
use App\Repository\MemberRepository;
use App\Schema\BookSchema;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    /**
     * Returns a list of service accounts associated with the user
     * @Route("/api/books", name="book_schema_list", methods={"GET"})
     * @return JsonResponse
     */
    public function listBooks(): JsonResponse
    {
        /** @var BookRepository $rep */
        $rep = $this->getDoctrine()->getRepository(Book::class);
        $result = $rep->findAll();
        return new JsonResponse(array_map('self::cb', $result));
    }

    private static function cb($data) : BookSchema
    {
        return BookSchema::MakeFromEntity($data);
    }

    /**
     * Create/Update a book
     * @Route("/api/books", name="book_schema_post", methods={"POST"})
     * @param Request $req
     * @return JsonResponse
     */
    public function saveBook(Request $req): JsonResponse
    {
        $schema = BookSchema::MakeFromRequest($req);
        $em = $this->getDoctrine()->getManager();

        if ($schema && $schema->id === null) {
            /** @var MemberRepository $mrep */
            $mrep = $em->getRepository(Member::class);
            $borrower = $mrep->findOneBy(array('id' => $schema->id));
            $book = new Book();
            $book->setFromSchema($schema, $borrower);
            $em->persist($book);
            $em->flush();
            return new JsonResponse(BookSchema::MakeFromEntity($book));
        } else if ($schema && $schema->id !== null) {
            /** @var MemberRepository $mrep */
            $mrep = $em->getRepository(Member::class);
            $borrower = $mrep->findOneBy(array('id' => $schema->id));
            /** @var BookRepository $rep */
            $rep = $this->getDoctrine()->getRepository(Book::class);

            $criteria = array('id' => $schema->id);
            $result = $rep->findOneBy($criteria);
            if ($result !== null) {
                $result->setFromSchema($schema, $borrower);
                $em->persist($result);
                $em->flush();
                return new JsonResponse(BookSchema::MakeFromEntity($result));
            } else {
                return new JsonResponse(['error' => 'Failed to update entity']);
            }
        } else {
            return new JsonResponse(['error' => 'Failed to create entity']);
        }
    }

    /**
     * Delete a book
     * @Route("/api/books", name="book_schema_delete", methods={"DELETE"})
     * @param Request $req
     * @return JsonResponse
     */
    public function deleteBook(Request $req): JsonResponse
    {
        $schema = BookSchema::MakeFromRequest($req);
        $em = $this->getDoctrine()->getManager();

        if ($schema && $schema->id !== null) {
            /** @var BookRepository $rep */
            $rep = $this->getDoctrine()->getRepository(Book::class);
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