<?php

namespace RedlineCms\Service;

use Cycle\ORM\EntityManager;
use RedlineCms\Core\Support\App;
use RedlineCms\Core\Support\Log;
use RedlineCms\Core\Support\Path;
use RedlineCms\Core\Support\ThemeManager;
use RedlineCms\Entity\Config;
use RedlineCms\Entity\User;
use RedlineCms\Repository\ConfigRepository;
use RedlineCms\Repository\UserRepository;

class AppConfig
{
    /**
     * @var Config
     */
    public $config;

    public function __construct(private readonly ConfigRepository $repo, private readonly UserRepository $userRepo)
    {
        $this->persistConfig();
        $this->initTheme();
    }

    private function persistConfig()
    {
        $this->config = $this->repo->first();

        if ($this->config) {
            return;
        }

        $config = new Config("Redline CMS", "/favicon.ico", "/logo.png");

        $manager = App::resolve(EntityManager::class);
        $manager->persist($config);
        $manager->run();

        $this->config = $config;

        // ensure demo user
        if (!$user = $this->userRepo->first()) {
            $user = new User(
                username: "poke@utpal.io",
                email: "poke@utpal.io",
                password: "poke@utpal.io"
            );

            $manager->persist($user);
            $manager->run();
        }
    }

    private function initTheme()
    {
        $theme = $this->config->getTheme();

        Log::info("AAA", ["theme" => $theme]);

        if (is_dir(Path::theme($theme))) {
            ThemeManager::changeTheme($theme);
        }
    }
}
