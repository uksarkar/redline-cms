<?php

namespace RedlineCms\Controller\Admin;

use Cycle\ORM\EntityManager;
use RedlineCms\Controller\Controller;
use RedlineCms\Core\Http\Request;
use RedlineCms\Core\Http\Response;
use RedlineCms\Core\Http\UploadFile;
use RedlineCms\Core\Support\App;
use RedlineCms\Core\Support\Storage;
use RedlineCms\Repository\ConfigRepository;
use RedlineCms\Service\AppConfig;

class SettingsController extends Controller
{
    public function __construct(protected ConfigRepository $repo) {}

    public function view()
    {
        return Response::view("pages/settings.html");
    }

    public function update(Request $request, EntityManager $manager)
    {
        $appConfig = App::resolve(AppConfig::class);
        
        /** @var \RedlineCms\Entity\Config */
        $config = $appConfig->config;

        $logo = UploadFile::get("logo");
        if ($logo) {
            $path = Storage::upload("/images", sprintf("%s.%s", uniqid("app-logo-"), $logo->ext), $logo);
            Storage::delete($config->getLogoPath());

            $config->setLogo($path);
        }

        $favicon = UploadFile::get("favicon");
        if ($favicon) {
            $path = Storage::upload("/images", sprintf("%s.%s", uniqid("app-favicon-"), $favicon->ext), $favicon);
            Storage::delete($config->getFaviconPath());

            $config->setFavicon($path);
        }

        $config->setAppName($request->getBody("app_name"));

        $manager->persist($config);
        $manager->run();

        return Response::redirect("/admin/settings", 301)->with(["message" => "Settings updated"]);
    }
}
