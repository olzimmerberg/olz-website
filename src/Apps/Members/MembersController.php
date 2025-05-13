<?php

namespace Olz\Apps\Members;

use Olz\Apps\Members\Components\OlzMembers\OlzMembers;
use Olz\Utils\HttpUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MembersController extends AbstractController {
    #[Route('/apps/mitglieder')]
    public function index(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzMembers $olzMembers,
    ): Response {
        $httpUtils->countRequest($request);
        $html_out = $olzMembers->getHtml([]);
        return new Response($html_out);
    }
}
