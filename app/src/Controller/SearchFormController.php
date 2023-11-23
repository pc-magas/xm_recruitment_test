<?php

namespace App\Controller;

use App\Exceptions\InvalidSymbolException;
use App\Services\SearchStockService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SearchFormController extends AbstractController
{
    #[Route('/', name: 'app_search_form')]
    public function index(): Response
    {
        return $this->render('search_form/index.html.twig', []);
    }

    #[Route('/search',name:'search',methods: ['POST'])]
    public function searchAjax(Request $request,SearchStockService $stockSearcher):JsonResponse
    {
        $submittedToken = $request->get('token');
        if (!$this->isCsrfTokenValid('searchform', $submittedToken)) {
            return new JsonResponse(['err'=>"Invalid CSRF"],Response::HTTP_BAD_REQUEST);
        }

        $email = $request->get('email')??"";
        $email = trim($email);

        if(empty($email) ||  !filter_var($email,FILTER_VALIDATE_EMAIL)){
            return new JsonResponse(['err'=>"Invalid EMAIL"],Response::HTTP_BAD_REQUEST);
        }

        // StockService Also validates Data
        $symbol = $request->get('symbol')??'';
        $unix_from = $request->get('from_unix');
        $unix_until= $request->get('until_unix');

        if(empty($unix_until) && empty($unix_from)){
            return new JsonResponse(['err'=>"Invalid Range"],Response::HTTP_BAD_REQUEST);
        }

        try{
            $results = $stockSearcher->fetchData($symbol,
                (new \DateTime())->setTimestamp($unix_from),
                (new \DateTime())->setTimestamp($unix_until),
            );
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['err'=>$e->getMessage()],Response::HTTP_BAD_REQUEST);
        } catch (\RuntimeException $r){
            return new JsonResponse(['err'=>$r->getMessage()],Response::HTTP_NOT_FOUND);
        } catch (\Exception $e){
            return new JsonResponse(['err'=>"Unexpected Error"],Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Mocked Data we will use the SearchStockService
        return new JsonResponse([
            'name'=>$symbol,'price'=>$results
        ]);
    }
}
