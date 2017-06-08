<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {service_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a service along with a provider.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      // Create the service class file
      $service_name = $this->argument('service_name');
      $template = file_get_contents(getcwd() . '/file_templates/ServiceClass');
      file_put_contents(getcwd() . '/app/Services/' . $service_name . 'Impl.php', str_replace('{{ SERVICE_NAME }}', $service_name, $template));

      // Create the interface file
      $template = file_get_contents(getcwd() . '/file_templates/ServiceContract');
      file_put_contents(getcwd() . '/app/Services/Contracts/I' . $service_name . '.php', str_replace('{{ SERVICE_NAME }}', $service_name, $template));

      // Create the provider file
      $template = file_get_contents(getcwd() . '/file_templates/ServiceProvider');
      file_put_contents(getcwd() . '/app/Providers/' . $service_name . 'Provider.php', str_replace('{{ SERVICE_NAME }}', $service_name, $template));
    }
}
