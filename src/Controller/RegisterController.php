<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\RegisterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    private RegisterService $registerService;

    public function __construct(RegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->json(
            $this->registerService->registerNewPlayer($request->request->all())
        );
    }
}
