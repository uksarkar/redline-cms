<?php

namespace RedlineCms\Controller;

use RedlineCms\Core\Http\Response;
use RedlineCms\Core\Support\App;
use RedlineCms\Core\Support\ThemeManager;
use RedlineCms\Service\ViewContext;

class HomeController extends Controller
{
    public function index()
    {
        return Response::view(ThemeManager::getTheme()->getTemplate("index.html"), ["context" => App::resolve(ViewContext::class)]);
    }
}
