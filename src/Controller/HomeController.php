<?php

namespace RedlineCms\Controller;

use RedlineCms\Core\Http\Response;
use RedlineCms\Core\Support\App;
use RedlineCms\Service\ViewContext;

class HomeController extends Controller
{
    public function index()
    {
        return Response::view("@themes/default/index.html", ["context" => App::resolve(ViewContext::class)]);
    }
}
