<?php

namespace ZnLib\Rpc\Symfony4\Web\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ZnLib\Web\Symfony4\MicroApp\BaseWebController;

class DefaultController extends BaseWebController
{

    protected $viewsDir = __DIR__ . '/../views/front';

    public function index(Request $request): Response
    {
        return $this->renderTemplate('index');
    }
}
