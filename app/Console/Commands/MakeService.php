<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service 
                            {name? : The name of the service class}
                            {--folder=Services : Folder where the class should be created}
                            {--test : Also generate a matching test class}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class with optional test file';

    /**
     * Execute the console command.
     */
    public function handle()
        {
            $name = $this->argument('name') ?? $this->ask('What is the name of the service class?');
            $className = Str::studly($name);

            $folder = $this->ask('What folder should the service be saved in? (default: Services)', 'Services');
            $folder = trim($folder) === '' ? 'Services' : $folder;

            $namespace = "App\\" . str_replace('/', '\\', $folder);
            $directory = app_path($folder);
            $filePath = "$directory/{$className}.php";

            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
                $this->info("✅ Created folder: $folder");
            }

            if (File::exists($filePath)) {
                return $this->error("❌ Service class already exists at: $filePath");
            }


            // Template
            $stub = <<<PHP
                <?php

                namespace $namespace;

                class {$className}
                {
                    public function handle()
                    {
                        // Handle the logic here
                    }
                }
                PHP;

        File::put($filePath, $stub);
        $this->info("✅ Service class created: {$className} in $folder");

            // Ask if user wants a test
            if ($this->confirm("Do you want to generate a unit test for {$className}?", true)) {
                $this->createTest($className, $folder);
            }
        }
        
        protected function createTest($className, $folder)
        {
            $folderPath = str_replace('\\', '/', $folder);
            $testDirectory = base_path("tests/Unit/$folderPath");
            $testPath = "$testDirectory/{$className}Test.php";

            if (!File::exists($testDirectory)) {
                File::makeDirectory($testDirectory, 0755, true);
                $this->info("📁 Created test folder: tests/Unit/$folderPath");
            }

            if (File::exists($testPath)) {
                $this->warn("⚠️ Test class already exists: $testPath");
                return;
            }

            $testStub = <<<PHP
                    <?php

                    namespace Tests\Unit\\$folder;

                    use Tests\TestCase;
                    use App\\$folder\\$className;

                    class {$className}Test extends TestCase
                    {
                        public function test_handle_method_exists()
                        {
                            \$service = new $className();
                            \$this->assertTrue(method_exists(\$service, 'handle'));
                        }
                    }
                    PHP;
            File::put($testPath, $testStub);
            $this->info("Test class created: {$className}Test");
        }
}
