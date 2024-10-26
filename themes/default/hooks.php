<?php

use Cycle\ORM\EntityManager;
use RedlineCms\Core\Http\Request;
use RedlineCms\Core\Http\Response;
use RedlineCms\Entity\ThemeMeta;
use RedlineCms\Repository\ThemeMetaRepository;

function default_get_colors_theme_meta(ThemeMetaRepository $repo)
{
    $colors = $repo->findByName("colors");
    if (!$colors) {
        $colors = new ThemeMeta("colors", [
            "header_color" => "#700303",
            "header_menu_color" => "#d9b219",
            "header_menu_active_color" => "#fed45e",
            "footer_items_color" => "#dbe9f5",
            "header_menu_hover_color" => "#c09a0b",
        ]);
    }

    return $colors;
}

add_hook("describe", [
    "name" => "Default Theme",
    "author" => "Utpal Sarkar",
    "description" => "This is a default theme by Redline CMS"
]);

add_hook("define_admin_routes", [
    [
        "path" => "/customize",
        "handler" => fn(ThemeMetaRepository $repo) => Response::view("@themes/default/customize.html", ["meta" => default_get_colors_theme_meta($repo)]),
        "label" => "Customize",
    ],
    [
        "path" => "/customize",
        "method" => "POST",
        "handler" => function (Request $request, ThemeMetaRepository $repo, EntityManager $manager) {
            $meta = default_get_colors_theme_meta($repo);
            $colors = $meta->getData();

            foreach ($request->body() as $key => $value) {
                $colors[$key] = $value;
            }

            $meta->setData($colors);
            $manager->persist($meta);
            $manager->run();

            return Response::redirect("/admin/custom/customize");
        }
    ]
]);
