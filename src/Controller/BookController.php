<?php

namespace App\Controller;


use App\Repository\BookRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class BookController extends AbstractController
{
    #[Route('/api/books', name: 'books', methods: ['GET'])]
    public function getAllBooks(BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {
        $bookList = $bookRepository->findAll();
        // dd($list);

        // $jsonBookList = $serializer->serialize($bookList, 'json');

        // ['groups'=>'getbooks'] me permet de retourner que les donnees qui m'interessent dans le cas des jointures
        $jsonBookList = $serializer->serialize($bookList, 'json', ["groups" => "getBooks"]);

        return new JsonResponse(
            $jsonBookList,
            Response::HTTP_OK,
            [],
            true
        );
    }


    #[Route('/api/books/{id}', name: 'detailBook', methods: ['GET'])]
    public function getOneBook(BookRepository $bookRepository, SerializerInterface $serializer, int $id): JsonResponse
    {
        $bookList = $bookRepository->find($id);
        // dd($list);
        if ($bookList) {
            $jsonBookList = $serializer->serialize($bookList, 'json', ['groups' => 'getBooks']);
            return new JsonResponse(
                $jsonBookList,
                Response::HTTP_OK,
                [],
                true
            );
        } else {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }
    }
}
