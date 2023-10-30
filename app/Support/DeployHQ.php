<?php
namespace App\Support;

use Exception;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Http;

class DeployHQ
{
    protected $apiUrl = 'https://<account>.deployhq.com';
    protected $headers = [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json'
    ];
    protected $username;
    protected $password;

    public function __construct()
    {
        $this->apiUrl = str_replace('<account>', env('DEPLOYHQ_ACCOUNT'), $this->apiUrl);
        $this->username = env('DEPLOYHQ_USER');
        $this->password = env('DEPLOYHQ_API_TOKEN');
    }

    public function getProject($projectID)
    {
        $response = $this->send('get', '/projects/'. $projectID);
        return $response->json();
    }

    public function getServer($projectID, $serverID)
    {
        $response = $this->send('get', '/projects/'. $projectID . '/servers/'. $serverID);
        return $response->json();
    }

    public function getServers($projectID)
    {
        $response = $this->send('get', '/projects/'. $projectID . '/servers');
        return $response->json();
    }

    public function getDeployments($projectID)
    {
        $response = $this->send('get', '/projects/'. $projectID . '/deployments');
        return $response->json();
    }

    public function createServer($projectID, $name, $ip, $domain, $atomic = true)
    {
        $response = $this->send('post', '/projects/'. $projectID . '/servers', [
            'server' => [
                'name' => $name,
                'protocol_type' => 'ssh',
                'server_path' => '/home/forge/' . $domain,

                'hostname' => $ip,
                'username' => 'forge',
                'use_ssh_keys' => true,
                'port' => 22,
                // 'atomic' => $atomic,
                // 'atomic_strategy' => 'copy_release'
            ]
        ]);
        return $response->json();
    }

    public function createConfigFile($projectID, $path, $body, $allServers = false, $serverIDs = [])
    {
        $response = $this->send('post', '/projects/'. $projectID . '/config_files', [
           'config_file' =>  [
                'path' => $path,
                'body' => $body,
                'all_servers' => $allServers,
                'server_identifiers'=> $serverIDs
            ]
        ]);
        return $response->json();
    }

    public function createDeployment($projectID, $serverID, $startRevision, $endRevision, $copyConfig = true, $runCommands = true, $useBuildCache = true, $branch = 'main')
    {
        $response = $this->send('post', '/projects/'. $projectID . '/deployments', [
            'deployment' => [
                'parent_identifier' => $serverID,
                'start_revision' => $startRevision,
                'end_revision' => $endRevision,
                'branch' => $branch,
                'mode' => 'queue',
                'copy_config_files' => $copyConfig,
                'run_build_commands' => $runCommands,
                'use_build_cache' => $useBuildCache
            ]
        ]);
        return $response->json();
    }

    public function getLatestRevision($projectID, $branch = null)
    {
        $branchQuery = $branch ? '?branch=' . $branch : '';
        $response = $this->send('get', '/projects/'. $projectID . '/repository/latest_revision' . $branchQuery);
        return $response->json();
    }

    public function getDeployment($projectID, $deploymentID)
    {
        $response = $this->send('get', '/projects/'. $projectID . '/deployments/' . $deploymentID);
        return $response->json();
    }

    public function send($method, $url, array $data = null)
    {
        $request = Http::withBasicAuth($this->username, $this->password)->withHeaders($this->headers);

        if ($data) {
            $response = $request->$method($this->apiUrl . $url, $data);
        } else {
            $response = $request->$method($this->apiUrl . $url);
        }

        if ($response->failed()) {
            throw new Exception($response->body());
        }

        return $response;
    }

    /* Helpers */
    public function configureConfigFiles($projectID, $serverID, $env, $client, $domain, $databaseName, $databaseUser, $databasePassword, $databaseConnection)
    {
        $exampleEnvContents = file_get_contents(base_path('.env.example'));

        $lines = explode("\n", $exampleEnvContents);
        if ($client != 'Base') {
            $lines[0] = 'APP_NAME="Senses '. $client .'"';
        }

        $appKey = 'base64:'.base64_encode(
            Encrypter::generateKey(config('app.cipher'))
        );

        $debug = ($env == 'staging') ? 'true' : 'false';
        $lines[1] = "APP_ENV=" . $env;
        $lines[2] = "APP_KEY=" . $appKey;
        $lines[3] = "APP_DEBUG=" . $debug;
        $lines[4] = "APP_URL=https://" . $domain;

        $lines[6] = "SENSES_CLIENT=" . $client;

        $lines[10] = "DB_CONNECTION=" . $databaseConnection;
        $lines[13] = "DB_DATABASE=" . $databaseName;
        $lines[14] = "DB_USERNAME=" . $databaseUser;
        $lines[15] = "DB_PASSWORD=" . $databasePassword;

        //modify example and then save it
        $content = implode("\n", $lines);

        $this->createConfigFile($projectID, '.env', $content, false, [$serverID]);
    }

    public function waitForDeploymentComplete($projectID, $deploymentID, callable $pendingCallback, $initalSleep = true, $attempts = 30)
    {
        $waitSeconds = 30;
        //initial wait, and then we will poll again for 2 mins as stated by forge api
        // Attempt 5 times while resting 2mins in between attempts...
        if ($initalSleep) {
            sleep($waitSeconds);
        }

        $attemptCount = 0;
        retry($attempts, function () use (&$pendingCallback, &$projectID, &$deploymentID, &$attemptCount) {
            $deployment = $this->getDeployment($projectID, $deploymentID);
            if (!(isset($deployment['status']) && $deployment['status'] == 'completed')) {
                $attemptCount++;
                $pendingCallback($deployment, $attemptCount);
                throw new Exception('Deployment running');
            }
        }, $waitSeconds * 1000);
    }
}
