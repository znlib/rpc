<?php

namespace ZnLib\Rpc\Symfony4\Web\Controllers;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ZnCore\Base\Libs\Http\Enums\HttpHeaderEnum;
use ZnLib\Web\Helpers\Url;
use ZnCore\Base\Libs\FileSystem\Helpers\FilePathHelper;
use ZnCore\Base\Libs\FileSystem\Helpers\MimeTypeHelper;
use ZnLib\Rpc\Domain\Enums\Rbac\RpcDocPermissionEnum;
use ZnLib\Rpc\Domain\Interfaces\Services\DocsServiceInterface;
use ZnLib\Web\Symfony4\MicroApp\BaseWebController;
use ZnLib\Web\Symfony4\MicroApp\Interfaces\ControllerAccessInterface;
use ZnLib\Web\Widgets\BreadcrumbWidget;

class DocsController extends BaseWebController implements ControllerAccessInterface
{

    private $docsService;
    protected $breadcrumbWidget;
    protected $viewsDir = __DIR__ . '/../views/docs';

    public function __construct(
        DocsServiceInterface $docsService,
        BreadcrumbWidget $breadcrumbWidget
    )
    {
        $this->docsService = $docsService;
        $this->breadcrumbWidget = $breadcrumbWidget;
        $title = 'JSON RPC';
        $this->breadcrumbWidget->add($title, Url::to(['/json-rpc']));
//        $this->getView()->addAttribute('title', $title);
    }

    public function access(): array
    {
        return [
            'index' => [
                RpcDocPermissionEnum::ALL
            ],
            'view' => [
                RpcDocPermissionEnum::ONE
            ],
            'download' => [
                RpcDocPermissionEnum::DOWNLOAD
            ],
        ];
    }

    public function index(Request $request): Response
    {
        $this->breadcrumbWidget->add('List docs', Url::to(['/json-rpc']));
        //$this->layout = __DIR__ . '/../../../../Common/views/layouts/main.php';
        $docs = $this->docsService->all();
        return $this->render('index', [
            'docs' => $docs,
        ]);
    }

    public function view(Request $request): Response
    {
        $name = $request->query->get('name', 'index');
        $docsHtml = $this->docsService->loadByName($name);
        $response = new Response($docsHtml);
        $response->send();
        exit();

//        return $response;
    }

    public function download(Request $request): Response
    {
        $name = $request->query->get('name', 'index');
        $docsHtml = $this->docsService->loadByName($name);

        $entity = $this->docsService->oneByName($name);
        $fileName = $name . '_' . date('Y-m-d_H-i-s') . '.html';

        $response = $this->downloadFileContent($docsHtml, $fileName);
        $response->send();
        exit();

//        return $response;
    }
}
