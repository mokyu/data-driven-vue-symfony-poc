<?php


namespace App\Controller;

use App\Enum\GenreEnum;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class EnumController
{
    /**
     * Returns a list of server enums
     * @Route("/api/enum/GenreEnum", name="server_enum_list", methods={"GET"})
     * @return JsonResponse
     */
    public function listGenreEnum(): JsonResponse
    {
        return new JsonResponse(GenreEnum::getAsList());
    }

}