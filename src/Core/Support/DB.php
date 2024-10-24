<?php

namespace RedlineCms\Core\Support;

use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\Config\SQLite\FileConnectionConfig;
use Cycle\Database\Config\SQLiteDriverConfig;
use Cycle\Database\DatabaseManager;
use Cycle\ORM\Factory;
use Cycle\ORM\ORM;
use Cycle\ORM\Schema;
use Cycle\Schema\Compiler;
use Cycle\Annotated;
use Cycle\Annotated\Locator\TokenizerEmbeddingLocator;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Schema\Generator\ForeignKeys;
use Cycle\Schema\Generator\GenerateModifiers;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderModifiers;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\ResetTables;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Generator\ValidateEntities;
use Cycle\Schema\Registry;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Spiral\Tokenizer\ClassLocator;

class DB
{
    private static function initDbManager()
    {
        return new DatabaseManager(
            new DatabaseConfig([
                'default' => 'default',
                'databases' => [
                    'default' => ['connection' => 'sqlite']
                ],
                'connections' => [
                    'sqlite' => new SQLiteDriverConfig(
                        connection: new FileConnectionConfig(Path::storage("redline_cms.db")),
                        queryCache: true,
                    ),
                ]
            ])
        );
    }

    public static function init()
    {
        // Initialize the database manager
        $dbManager = static::initDbManager();

        // Set up entity and embedding locators
        $finder = (new \Symfony\Component\Finder\Finder())->files()->in([Path::src("Entity")]);
        $classLocator = new ClassLocator($finder);
        $embeddingLocator = new TokenizerEmbeddingLocator($classLocator);
        $entityLocator = new TokenizerEntityLocator($classLocator);

        // Compile the schema
        $schema = (new Compiler())->compile(new Registry($dbManager), [
            new ResetTables(),
            new Annotated\Embeddings($embeddingLocator),
            new Annotated\Entities($entityLocator),
            new Annotated\TableInheritance(),
            new Annotated\MergeColumns(),
            new GenerateRelations(),
            new GenerateModifiers(),
            new ValidateEntities(),
            new RenderTables(),
            new RenderRelations(),
            new RenderModifiers(),
            new ForeignKeys(),
            new Annotated\MergeIndexes(),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        // setup logger
        $logger = new Logger('redline-cms');
        $logger->pushHandler(new StreamHandler(Path::storage("logs/queries.log")));

        $dbManager->setLogger($logger);

        // Return the ORM instance
        return new ORM(new Factory($dbManager), new Schema($schema));
    }
}
