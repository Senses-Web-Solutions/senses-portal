<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
// use App\Support\SensesUUID; //I hate this
use Laravel\Passport\Passport;
use App\Support\DatabaseChannel;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Facade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\RateLimiter;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\Channels\DatabaseChannel as IlluminateDatabaseChannel;

class SensesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        View::addNamespace('Base', base_path('Clients/base/Resources/views'));
        Blade::anonymousComponentPath(base_path('Clients/base/Resources/views/components'), 'client');
        Blade::anonymousComponentPath(base_path('Clients/base/Resources/views/components'), 'base');

        View::macro('client', function ($view, $data = []) {
            return view()->first([
                'Client::' . $view,
                'Base::'. $view,
            ], $data);
        });

        Blade::directive('client', function (...$args) {
            $args = implode(",", $args);
            return "<?php echo view()->client($args)->render(); ?>";
        });

        Passport::cookie('senses_token');

        // AboutCommand::add('Senses', app(\App\Actions\Server\AboutChecklist::class)->execute());

        Relation::morphMap([
            'App\Models\User'
        ]);

        //        Blueprint::macro('colour', function ($field = 'colour', $defaultColour = '#dddddd') {
        //            $this->string($field, 7)->default($defaultColour);
        //            $this->string('text_' . $field, 7)->default(textColour($defaultColour));
        //        });

        RateLimiter::for("login", function () {
            Limit::perMinute(5);
        });

        Blueprint::macro('colour', function ($field = 'colour', $defaultColour = 'purple-light') {
            $this->string($field)->default($defaultColour);
            $this->string('text_' . $field)->default(textColour($defaultColour));
        });

        Blueprint::macro('geom', function ($field) {
            $this->geometry($field)->isGeometry()->projection(27700);
            $this->spatialIndex($field);
        });

        Blueprint::macro('money', function ($field) {
            return $this->decimal($field, 15, 4);
        });

        Blueprint::macro('sensesTimestamps', function () {
            $this->unsignedBigInteger('created_by')->nullable()->index();
            $this->unsignedBigInteger('updated_by')->nullable()->index();
            $this->unsignedBigInteger('deleted_by')->nullable()->index();
            $this->timestamps();
            $this->softDeletes();
        });

        Blueprint::macro('sensesHiddenAt', function(){
            $this->timestamp('hidden_at')->nullable();
            $this->unsignedBigInteger('hidden_by')->nullable()->index();
        });

        Blueprint::macro('lockable', function () {
            $this->unsignedBigInteger('locked_by')->nullable()->index();
            $this->timestamp('locked_at')->nullable();
            $this->string('lock_type')->nullable();
        });

        Builder::macro('appFindOrFail', function($id) {
            return $this->when(is_numeric($id), function($q) use(&$id) {
                $q->where('id', $id);
            }, function($q) use(&$id) {
                $q->where('uuid', $id);
            })
            ->firstOrFail();
        });

        Builder::macro('appFind', function($id) {
            return $this->when(is_numeric($id), function($q) use(&$id) {
                $q->where('id', $id);
            }, function($q) use(&$id) {
                $q->where('uuid', $id);
            })
            ->first();
        });

        Builder::macro('toSqlWithBindings', function () {
            $bindings = array_map(
                fn ($value) => is_numeric($value) ? $value : "'{$value}'",
                $this->getBindings()
            );

            return Str::replaceArray('?', $bindings, $this->toSql());
        });

        Builder::macro('list', function (string $type = 'paged') {
            $limit = min(2000, request()->input('limit', 25));
            $format = request()->input('format', $type);

            if(request()->has('group_by')) {
                return $this->tableOptions($limit);
            }

            return match ($format) {
                'all' => $this->get(),
                'limited' => $this->limit($limit)->get(),
                'select-search' => $this->limit(20)->get(),
                'cursor' => $this->cursorPaginate($limit)->appends(request()->query()),
                'paged' => $this->paginate($limit)->appends(request()->query()),
                'paged-deferred' => $this->deferredPaginate($limit)->appends(request()->query()),
                'table-options' => $this->tableOptions($limit),
                default => $this->paginate($limit)->appends(request()->query()),
            };
        });

        Builder::macro('tableOptions', function($limit = null) {
            $groupBy = request()->input('group_by');

            $model = $this->newModelInstance();
            $table = $model->getTable();
            $this->query
            ->when($groupBy, function($q) use($table, $groupBy)
            {
                $q->groupBy($groupBy);
            });
            // ->when($limit, fn($q) => $q->limit($limit));
            $column = str_replace('.', '__', $groupBy);

            // dd($this->toSql());
            return $this->pluck($column)->toArray(); //array bypasses appends onto a collection

        });

        Builder::macro('deferredPaginate', function ($perPage = null, $columns = ['*'], $pageName = 'page', $page = null) {
            $model = $this->newModelInstance();
            $key = $model->getKeyName();
            $table = $model->getTable();

            $paginator = $this->clone()
                // We don't need them for this query, they'll remain
                // on the query that actually gets the records.
                ->setEagerLoads([])
                // Only select the primary key, we'll get the full
                // records in a second query below.
                ->paginate($perPage, ["$table.$key"], $pageName, $page);

            // Add our values in directly using "raw," instead of adding new bindings.
            // This is basically the `whereIntegerInRaw` that Laravel uses in some
            // places, but we're not guaranteed the primary keys are integers, so
            // we can't use that. We're sure that these values are safe because
            // they came directly from the database in the first place.
            $this->query->wheres[] = [
                'type'   => 'InRaw',
                'column' => "$table.$key",
                // Get the key values from the records on the *current* page, without mutating them.
                'values'  => $paginator->getCollection()->map->getRawOriginal($key)->toArray(),
                'boolean' => 'and'
            ];

            // simplePaginate increments by one to see if there's another page. We'll
            // decrement to counteract that since it's unnecessary in our situation.
            $page = $this->simplePaginate($paginator->perPage() - 1, $columns, null, 1);

            // Create a new paginator so that we can put our full records in,
            // not the ones that we modified to select only the primary key.
            return new LengthAwarePaginator(
                $page->items(),
                $paginator->total(),
                $paginator->perPage(),
                $paginator->currentPage(),
                $paginator->getOptions()
            );
        });

        Str::macro('initials', function ($string, int|null $length = 5) {
            $initials = [];
            $string = str_replace(['-', '_', '.', '|'], ' ', $string);
            $parts = explode(' ', $string);
            foreach ($parts as $part) {
                array_push($initials, substr($part, 0, 1));
            }
            $initials = array_slice($initials, 0, $length);
            return strtoupper(implode('', $initials));
        });

        $this->app->instance(IlluminateDatabaseChannel::class, new DatabaseChannel);
        Facade::clearResolvedInstance(IlluminateDatabaseChannel::class);

        // Factory::macro('createQuietlyWithUUID', function ($attributes = [], ?Model $parent = null) {
        //     $attributes['uuid'] = SensesUUID::generate();
        //     return $this->createQuietly($attributes, $parent);
        // });

        View::macro('clientFirst', function (array $views, Arrayable|array $data = [], array $mergeData = []) {
            $clientViews = [];
            foreach ($views as $view) {
                array_push($clientViews, 'Client::' . $view);
                array_push($clientViews, $view);
            }
            // dd($clientViews);
            return View::first($clientViews, $data, $mergeData);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //Register Senses client
        // $client = env('SENSES_CLIENT');
        // $client = config('senses.client');
        // $provider = "\Clients\\$client\SensesServiceProvider";
        // if (class_exists($provider)) {
        //     $this->app->register($provider);
        // }

        // if ($this->app->isLocal()) {
        //     $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
        // }
    }
}
