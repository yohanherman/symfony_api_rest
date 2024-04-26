<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthorController extends AbstractController
{
    #[Route('/api/authors', name: 'author', methods: ['GET'])]
    public function getAllAuthors(AuthorRepository $authorRepository, SerializerInterface $serializer): JsonResponse
    {
        $authors = $authorRepository->findAll();
        // dd($authors);

        $jsonAuthorList = $serializer->serialize($authors, 'json', ["groups" => "getAuthors"]);
        return new JsonResponse(
            $jsonAuthorList,
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/api/authors/{id}', name: "detailAuthor", methods: ["GET"])]
    public function getOneAuthor(AuthorRepository $authorRepository, SerializerInterface $serializer, int $id): JsonResponse
    {
        $author = $authorRepository->find($id);
        // dd($author);

        if ($author) {
            $jsonAuthor = $serializer->serialize($author, 'json', ['groups' => 'getAuthors']);

            return new JsonResponse(
                $jsonAuthor,
                Response::HTTP_OK,
                [],
                true
            );
        } else {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }
    }


    #[Route('/api/authors/{id}', name: 'deleteAuthor', methods: ["DELETE"])]
    public function deleteAuthor(EntityManagerInterface $entityManager, Author $author): JsonResponse
    {
        $entityManager->remove($author);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }


    #[Route('/api/authors', name: 'createAuthor', methods: ['POST'])]
    public function createAuthor(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator): JsonResponse
    {
        $author = $serializer->deserialize($request->getContent(), Author::class, 'json');

        $errors = $validator->validate($author);

        if (count($errors) > 0) {

            return new JsonResponse(
                $serializer->serialize($errors, 'json'),
                Response::HTTP_BAD_REQUEST,
                [],
                true
            );
        }

        $entityManager->persist($author);
        $entityManager->flush();



        $jsonAuthor = $serializer->serialize($author, 'json', ['groups' => 'getAuthors']);


        $location = $urlGenerator->generate('detailAuthor', ['id' => $author->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse(
            $jsonAuthor,
            Response::HTTP_CREATED,
            ['location' => $location],
            true
        );
    }




    #[Route('/api/authors/{id}', name: 'updateAuthor', methods: ['PUT'])]
    public function updateAuthor(Author $currentAuthor, EntityManagerInterface $entityManager, SerializerInterface $serializer, Request $request)
    {

        $updateAuthor = $serializer->deserialize($request->getContent(), Author::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $currentAuthor]);
        $entityManager->persist($updateAuthor);
        $entityManager->flush();


        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
