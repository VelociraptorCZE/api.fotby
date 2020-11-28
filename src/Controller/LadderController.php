<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\LadderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LadderController extends AbstractController
{
    private LadderService $ladderService;

    public function __construct(LadderService $ladderService)
    {
        $this->ladderService = $ladderService;
    }

    /**
     * @Route("/ladder", name="ladder")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->json(
            $this->ladderService->getLadderData($request->request->all())
        );
    }
}
