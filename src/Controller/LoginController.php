<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\LoginService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    private LoginService $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    /**
     * @Route("/login", name="login")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->json(
            $this->loginService->login($request->request->all())
        );
    }
}
