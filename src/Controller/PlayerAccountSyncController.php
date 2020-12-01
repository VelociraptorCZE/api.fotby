<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\PlayerAccountSyncService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlayerAccountSyncController extends AbstractController
{
    private PlayerAccountSyncService $playerAccountSyncService;

    public function __construct(PlayerAccountSyncService $playerAccountSyncService)
    {
        $this->playerAccountSyncService = $playerAccountSyncService;
    }

    /**
     * @Route("/account/sync", name="player_account_sync")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->json($this->playerAccountSyncService->sync($request->request->all()));
    }
}
