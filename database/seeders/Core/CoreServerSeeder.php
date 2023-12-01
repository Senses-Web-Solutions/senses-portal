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

        $servers = ["Dev", "Teamleaf 8", "CI", "Test", "Teamleaf 6"];

        for ($i=0; $i < 5; $i++) {
            $server = Server::create([
                "company_id" => $company->id,

                "title" => $servers[$i],

                "hostname" => $servers[$i],
                "ip_address" => "165.227.225.119",

                "os" => "GNU/Linux",
                "architecture" => "x86_64",

                "cpu_cores" => 4,
                "cpu_threads" => 4,

                "distro" => "Ubuntu",
                "distro_version" => "",

                "kernel" => "Linux",
                "kernel_version" => "",

                "verified_at" => now(),
            ]);

            $serverMetrics = ServerMetric::factory()->count(100)->create([
                'server_id' => $server->id,
                'company_id' => 1,
            ]);
        }
    }
}
