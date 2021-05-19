<?php

namespace ZnLib\Rpc\Symfony4\Web\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ZnLib\Rpc\Domain\Interfaces\Services\DocsServiceInterface;

class DocsController
{

    private $docsService;

    public function __construct(DocsServiceInterface $docsService)
    {
        $this->docsService = $docsService;
    }

    public function view(Request $request): Response
    {
        $name = $request->query->get('name', 'partner');
        $docsHtml = $this->docsService->loadByName($name);
        return new Response($docsHtml);
    }
}
