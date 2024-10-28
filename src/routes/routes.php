<?php

use RedlineCms\Controller\Admin\AdminCategoryController;
use RedlineCms\Controller\Admin\AdminPageController;
use RedlineCms\Controller\Admin\AdminPostController;
use RedlineCms\Core\Http\Route;
use RedlineCms\Middleware\AuthMiddleware;
use RedlineCms\Middleware\GuestMiddleware;
use RedlineCms\Controller\Admin\AuthController;
use RedlineCms\Controller\Admin\DashboardController;
use RedlineCms\Controller\Admin\SettingsController;
use RedlineCms\Controller\Admin\ThemeController;
use RedlineCms\Controller\Admin\UserController;
use RedlineCms\Controller\CategoryController;
use RedlineCms\Controller\HomeController;
use RedlineCms\Controller\PageController;
use RedlineCms\Controller\PostController;

Route::get("/", HomeController::class, "index");
Route::get("/posts/{slug}", PostController::class, "view");
Route::get("/pages/{slug}", PageController::class, "view");
Route::get("/category/{slug}", CategoryController::class, "view");

// Admin auth routes
Route::group(["path" => "/admin", "middlewares" => [new GuestMiddleware(redirectTo: "/admin")]], function () {
    Route::get("/login", AuthController::class, "loginView");
    Route::post("/login", AuthController::class, "login");
});

// Admin routes
Route::group(["path" => "/admin", "middlewares" => [AuthMiddleware::class]], function () {
    Route::get("/logout", AuthController::class, "logout");
    Route::get("/", DashboardController::class, "index");

    Route::get("/categories", AdminCategoryController::class, "index");
    Route::post("/category", AdminCategoryController::class, "store");
    Route::get("/category/{id}/edit", AdminCategoryController::class, "edit");
    Route::post("/category/{id}/edit", AdminCategoryController::class, "update");
    Route::post("/category/{id}/delete", AdminCategoryController::class, "delete");
    Route::post("/category/{id}/restore", AdminCategoryController::class, "restore");

    Route::get("/posts", AdminPostController::class, "index");
    Route::get("/post/create", AdminPostController::class, "create");
    Route::post("/post", AdminPostController::class, "store");
    Route::get("/post/{id}/edit", AdminPostController::class, "edit");
    Route::post("/post/{id}/edit", AdminPostController::class, "update");
    Route::post("/post/{id}/delete", AdminPostController::class, "delete");
    Route::post("/post/{id}/restore", AdminPostController::class, "restore");

    Route::get("/pages", AdminPageController::class, "index");
    Route::get("/page/create", AdminPageController::class, "create");
    Route::get("/page/{id}/edit", AdminPageController::class, "edit");

    Route::get("/settings", SettingsController::class, "view");
    Route::post("/settings", SettingsController::class, "update");

    Route::get("/themes", ThemeController::class, "index");
    Route::post("/themes", ThemeController::class, "update");

    Route::get("/users/{id}", UserController::class, "view");
    Route::post("/users/{id}", UserController::class, "update");
});
