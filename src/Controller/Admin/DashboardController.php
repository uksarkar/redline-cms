<?php

namespace RedlineCms\Controller\Admin;

use RedlineCms\Controller\Controller;
use RedlineCms\Core\Http\Response;

class DashboardController extends Controller
{
    public function index()
    {
        return Response::view("pages/dashboard.html");
    }
}
