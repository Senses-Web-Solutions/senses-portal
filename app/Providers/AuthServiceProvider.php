<?php

namespace App\Providers;

use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Auth::viaRequest('server-token', function (Request $request) {
            foreach (Server::has('apiTokens')->get() as $server) {
                foreach ($server->apiTokens as $token) {
                    $hasNoExpiry = $token->expires_at == null;
                    $hasValidExpiry = $token->expires_at >= now();
                    $hasValidHash = Hash::check($request->bearerToken(), $token->hash);

                    if (($hasNoExpiry || $hasValidExpiry) && $hasValidHash) {
                        return $server;
                    }
                }
            }

            return null;
        });
    }
}
