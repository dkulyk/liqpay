<?php

namespace DKulyk\LiqPay;

use Illuminate\Console\Command;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Filesystem\Filesystem;

class LiqPayTableCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'liqpay:table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a migration for the liqpay database table';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new liqpay table command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function fire()
    {
        $fullPath = $this->createBaseMigration();

        $this->files->put($fullPath, $this->files->get(__DIR__.'/stubs/database.stub'));

        $this->info('Migration created successfully!');

        $this->call('dump-autoload');
    }

    /**
     * Create a base migration file for the session.
     *
     * @return string
     */
    protected function createBaseMigration()
    {
        /**
         * @var MigrationCreator $migrationCreator
         */
        $name = 'create_liqpay_table';

        $path = $this->laravel->make('path').'/database/migrations';
        $migrationCreator = $this->laravel->make('migration.creator');

        return $migrationCreator->create($name, $path);
    }

}
