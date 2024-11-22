<?php

use Cycle\ORM\EntityManager;
use RedlineCms\Core\Http\Request;
use RedlineCms\Core\Http\Response;
use RedlineCms\Entity\MetaData;
use RedlineCms\Repository\MetaDataRepository;

function default_get_colors_theme_meta(MetaDataRepository $repo)
{
    return $repo->firstOrNew("colors", [
        "header_color" => "#700303",
        "header_menu_color" => "#d9b219",
        "header_menu_active_color" => "#fed45e",
        "footer_items_color" => "#dbe9f5",
        "header_menu_hover_color" => "#c09a0b",
    ]);
}

function default_get_slots_meta(MetaDataRepository $repo)
{
    return $repo->firstOrNew("slots", [
        "top_right" => "",
        "container_top" => "",
        "container_bottom" => "",
        "post_right" => ""
    ]);
}

add_hook("describe", [
    "name" => "Default Theme",
    "author" => "Utpal Sarkar",
    "description" => "This is a default theme by Redline CMS",
    "screenshot" => "/assets/default-theme-preview.png"
]);

add_hook("define_admin_routes", [
    [
        "path" => "/customize",
        "handler" => fn(MetaDataRepository $repo) => Response::view("@themes/default/customize.html", ["meta" => default_get_colors_theme_meta($repo)]),
        "label" => "Customize",
        "icon" => "customize-computer",
    ],
    [
        "path" => "/contacts",
        "handler" => fn(MetaDataRepository $repo) => Response::view("@themes/default/contacts.html", ["contacts" => $repo->getAll("contacts")]),
        "label" => "Contacts",
        "icon" => "address-book"
    ],
    [
        "path" => "/customize",
        "method" => "POST",
        "handler" => function (Request $request, MetaDataRepository $repo, EntityManager $manager) {
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
    ],
    [
        "path" => "/contacts/{id}/delete",
        "method" => "POST",
        "handler" => function(int $id, MetaDataRepository $repo, EntityManager $manager) {
            $entry = $repo->findByPK($id);

            if($entry) {
                $manager->delete($entry);
                $manager->run();
            }

            return Response::back();
        }
    ],
    [
        "path" => "/slots",
        "handler" => fn(MetaDataRepository $repo) => Response::view("@themes/default/admin/slots.html", ["meta" => default_get_slots_meta($repo)]),
        "label" => "Slots",
        "icon" => "block-quote",
    ],
    [
        "path" => "/slots",
        "method" => "POST",
        "handler" => function (Request $request, MetaDataRepository $repo, EntityManager $manager) {
            $meta = default_get_slots_meta($repo);
            $slots = $meta->getData();

            foreach ($request->body() as $key => $value) {
                $slots[$key] = $value;
            }

            $meta->setData($slots);
            $manager->persist($meta);
            $manager->run();

            return Response::redirect("/admin/custom/slots");
        }
    ],
]);

add_hook("define_routes", [
    [
        "path" => "contact",
        "method" => "POST",
        "handler" => function (Request $request, EntityManager $manager) {
            $data = $request->only(["name", "email", "message"]);

            if (count($data) !== 3) {
                return Response::back(["error" => true]);
            }

            $meta = new MetaData("contacts", $data);
            $manager->persist($meta);
            $manager->run();

            return Response::back(["success" => true]);
        }
    ]
]);
