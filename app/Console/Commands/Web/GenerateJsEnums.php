<?php

namespace App\Console\Commands\Web;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use MyCLabs\Enum\Enum;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class GenerateJsEnums extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'enums:publish_frontend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Converts enums to js objects for the frontend.';

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
     * @return int
     */
    public function handle()
    {
        $enums = base_path('app/Enums');

        $this->load($enums);

        return 0;
    }

    protected function load($paths)
    {
        $paths = array_unique(Arr::wrap($paths));

        $paths = array_filter($paths, function ($path) {
            return is_dir($path);
        });

        if (empty($paths)) {
            return;
        }

        foreach ((new Finder)->in($paths)->files() as $enum) {
            $enum = 'App\\' . str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($enum->getRealPath(), realpath(app_path()) . DIRECTORY_SEPARATOR)
            );

            if (!class_exists($enum)) {
                continue;
            }

            $js = "// This is a generated file. To regenerate, run php artisan enum:publish_frontend \n";
            $js .= "// Published at " . now()->format('Y-m-d H:i:s') . "\n";
            $js .= "\n";
            $js .= 'export default ';
            $js .= json_encode($enum::toArray(), JSON_PRETTY_PRINT) . "\n";
            $js .= "\n";

            if (method_exists($enum, 'getLabels')) {
                $labels = $enum::getLabels();
                $js .= 'export const LabelMap = ';
                $js .= json_encode($labels, JSON_PRETTY_PRINT) . "\n";
                $js .= "\n";


                $labelSelect = array_map(fn ($key) => ['title' => $labels[$key], 'id' => $key], array_keys($labels));
                $js .= 'export const LabelSelectMap = ';
                $js .= json_encode($labelSelect, JSON_PRETTY_PRINT) . "\n";
                $js .= "\n";
            }

            if (method_exists($enum, 'colours')) {
                $colours = $enum::colours();
                $js .= 'export const ColourMap = ';
                $js .= json_encode($colours, JSON_PRETTY_PRINT) . "\n";
                $js .= "\n";
            }

            $name = explode('\\', $enum)[count(explode('\\', $enum)) - 1];

            // Skip format, JS date formats are different to PHP ones.
            if ($name !== 'Format') {
                file_put_contents(base_path('resources/js/Enums/' . $name . '.js'), $js);
                $this->info('Stored ' . $enum);
            } else {
                $this->info('Skipped ' . $name . 's');
            }
        }
    }
}
