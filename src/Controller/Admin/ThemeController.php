<?php

namespace RedlineCms\Controller\Admin;

use Cycle\ORM\EntityManager;
use RedlineCms\Controller\Controller;
use RedlineCms\Core\Http\Request;
use RedlineCms\Core\Http\Response;
use RedlineCms\Core\Support\App;
use RedlineCms\Core\Support\Path;
use RedlineCms\Core\Support\Storage;
use RedlineCms\Core\Support\ThemeManager;
use RedlineCms\Repository\ConfigRepository;
use RedlineCms\Service\AppConfig;

class ThemeController extends Controller
{
    public function __construct(protected ConfigRepository $repo) {}

    public function index()
    {
        $themes = array_map(fn($theme) => ThemeManager::getThemeData($theme), Storage::listDir(Path::theme()));
        return Response::view("pages/themes.html", compact("themes"));
    }

    public function update(Request $request, EntityManager $manager)
    {
        $appConfig = App::resolve(AppConfig::class);
        $config = $appConfig->config;

        $config->setTheme($request->getBody("theme", "default"));

        $manager->persist($config);
        $manager->run();

        return Response::redirect("/admin/themes", 301)->with(["message" => "Settings updated"]);
    }
}
