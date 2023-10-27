<?php

namespace App\Providers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Queue\Events\JobProcessing;

use App\Models\User;
use App\Observers\UserObserver;
use App\Models\File;
use App\Observers\FileObserver;
use App\Models\StatusGroup;
use App\Observers\StatusGroupObserver;
use App\Models\Status;
use App\Observers\StatusObserver;
use App\Models\TagGroup;
use App\Observers\TagGroupObserver;
use App\Models\Tag;
use App\Observers\TagObserver;
use App\Observers\ServerObserver;
use App\Models\Server;
// ----- GENERATOR 1 -----

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
		User::observe(UserObserver::class);
        File::observe(FileObserver::class);
        StatusGroup::observe(StatusGroupObserver::class);
        Status::observe(StatusObserver::class);
        TagGroup::observe(TagGroupObserver::class);
        Tag::observe(TagObserver::class);
		Server::observe(ServerObserver::class);
		// ----- GENERATOR 2 -----

        View::composer('*', function ($view) {
            if(!auth()->check()) {
                $view->with('currentUser', null);
                return;
            }
            $user = getCurrentUser();

            $user->abilities = $user->getAbilities();
            $user->roles = $user->getRoles();

            $view->with('currentUser', $user);
        });

		Queue::createPayloadUsing(function ($connection, $queue, $payload) {
			$jobData = [];
			$jobData['current_user_id'] = optional(getCurrentUserOrSystemUser())->id;
			$jobData['log_type'] = request()->log_type ?? null;
			$jobData['log_target'] = request()->log_target ?? null;
			$jobData['log_uuid'] = request()->log_uuid ?? null;

			return ['senses' => $jobData];
		});

        Queue::before(function (JobProcessing $event) {
			$payload = $event->job->payload()['senses'];

			$jobUserID = isset($payload['current_user_id']) ? $payload['current_user_id'] : null;
			$jobUser = $jobUserID ? \App\Models\User::find($jobUserID) : null; //set to current_user_id null to avoid this hit.
			$logType = isset($payload['log_type']) ? $payload['log_type'] : null;
			$logTarget = isset($payload['log_target']) ? $payload['log_target'] : null;
			$logUUID = isset($payload['log_uuid']) ? $payload['log_uuid'] : null;

			$request = new Request([
				'job_user' => $jobUser,
				'log_type' => $logType,
				'log_target' => $logTarget,
				'log_uuid' => $logUUID,
			], server: request()->server());

			//sets a request on the app, Illuminate\Foundation\Http\Kernel.php line 133
			app()->instance('request', $request);
			Facade::clearResolvedInstance('request');
        });
    }
}
