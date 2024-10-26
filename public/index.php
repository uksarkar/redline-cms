<?php

/**
 * 
 * Redline CMS
 * 
 * A simple and lightweight CMS
 * @author connect@utpal.io <Utpal Sarkar>
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/routes.php';

use Cycle\ORM\EntityManager;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\ORMInterface;
use FastRoute\RouteCollector;
use RedlineCms\Core\Http\Middleware;
use RedlineCms\Core\Http\Request;
use RedlineCms\Core\Http\Response;
use RedlineCms\Core\Http\Route;
use RedlineCms\Core\Support\App;
use RedlineCms\Core\Support\DB;
use RedlineCms\Core\Support\Path;
use RedlineCms\Entity\Category;
use RedlineCms\Entity\Config;
use RedlineCms\Entity\DbSession;
use RedlineCms\Entity\Post;
use RedlineCms\Entity\User;
use RedlineCms\Repository\CategoryRepository;
use RedlineCms\Repository\ConfigRepository;
use RedlineCms\Repository\PostRepository;
use RedlineCms\Repository\UserRepository;
use RedlineCms\Service\AppConfig;
use RedlineCms\Service\AuthUser;
use RedlineCms\Service\Symlink;

/**
 * Build the PHP-DI container
 */
$orm = DB::init();
App::init([
    // ORM
    EntityManagerInterface::class => new EntityManager($orm),
    ORMInterface::class => $orm,

    // Services
    AuthUser::class => new AuthUser($orm->getRepository(User::class), $orm->getRepository(DbSession::class), new Request()),

    // Repos
    UserRepository::class => fn() => $orm->getRepository(User::class),
    PostRepository::class => fn() => $orm->getRepository(Post::class),
    DbSession::class => fn() => $orm->getRepository(DbSession::class),
    CategoryRepository::class => fn() => $orm->getRepository(Category::class),
    ConfigRepository::class => fn() => $orm->getRepository(Config::class),
]);
App::resolve(AppConfig::class);

/**
 * END of the PHP-DI container implementation
 */

// Register routes
$dispatcher = FastRoute\simpleDispatcher(fn(RouteCollector $r) => Route::init($r));

// Fetch method and URI from the request
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Remove query string (?foo=bar) from URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

if (!realpath(__DIR__ . "/storage")) {
    Symlink::createSymlink(Path::storage("/public"), __DIR__ . "/storage");
}

// Dispatch the route
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        Response::view(
            "error.html",
            ["error" => 404],
            404,
        )->send();
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        echo '405 Method Not Allowed';
        break;
    case FastRoute\Dispatcher::FOUND:
        // Create the response using the Response class
        // $routeInfo[1] = [Controller::class, "method", []Middleware]
        // $routeInfo[2] = [] available route params
        [$controller, $method, $middlewares] = $routeInfo[1];
        $params = $routeInfo[2];

        App::setParams($params);

        $next = fn() => App::getContainer()->call([$controller, $method], $params);

        // apply route middlewares
        if ($middlewares && is_array($middlewares) && count($middlewares) > 0) {
            foreach ($middlewares as $middleware) {
                if (is_string($middleware)) {
                    $middleware = App::resolve($middleware);
                }

                if ($middleware instanceof Middleware) {
                    $next = fn() => $middleware->handle($next);
                }
            }
        }

        // Send the response
        Response::create($next())->send();
        break;
}
