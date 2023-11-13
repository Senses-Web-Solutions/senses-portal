<?php

namespace Database\Seeders\Core;

use App\Models\Company;
use App\Models\Server;
use App\Models\ServerMetric;
use App\Models\Subscription;
use Illuminate\Database\Seeder;

class CoreServerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company = Company::create([
            "title" => "Senses"
        ]);

        $subscription = Subscription::create([
            "company_id" => $company->id,
            "type" => "server-monitor",

            "data" => [
                "max_servers" => 5
            ]
        ]);

        $server = Server::create([
            "company_id" => $company->id,

            "title" => "Dev",

            "hostname" => "Teamleaf8-Dev",
            "ip_address" => "165.227.225.119",

            "os" => "GNU/Linux",
            "architecture" => "x86_64",

            "cpu_cores" => 4,
            "cpu_threads" => 4,

            "distro" => "Ubuntu",
            "distro_version" => "",

            "kernel" => "Linux",
            "kernel_version" => ""
        ]);

        $serverMetrics = ServerMetric::factory()->count(100)->create([
            'server_id' => 1,
            'company_id' => 1,
        ]);
    }
}
