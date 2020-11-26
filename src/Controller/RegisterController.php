<?php
declare(strict_types=1);

namespace App\Controller;

use App\Config;
use App\Service\PasswordEncryptionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    private $passwordEncryptionService;

    public function __construct(PasswordEncryptionService $passwordEncryptionService)
    {
        $this->passwordEncryptionService = $passwordEncryptionService;
    }

    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $payload = $request->request->all();

        if (
            !isset($payload['username'])
            || !isset($payload['password'])
            || !isset($payload['apiKey'])
            || $payload['apiKey'] !== Config::SECRET_API_KEY
        ) {
            return $this->getIncorrectResponse();
        }

        $username = $payload['username'];
        $hash = $this->passwordEncryptionService->encryptPassword($payload['password']);

        // TODO: implement further logic

        return $this->json([
            'result' => true,
            'message' => 'Account has been successfully created'
        ]);
    }

    private function getIncorrectResponse(): Response
    {
        return $this->json([
            'result' => false,
            'message' => 'Account cannot be created'
        ]);
    }
}
