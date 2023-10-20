<?php
namespace App\Traits;

use Illuminate\Support\Str;

trait ChunkSeeder {
    public function chunkCreate($class, $totalChunks, $chunkSize, Callable $beforeCreateCallback = null) {
        $this->command->getOutput()->writeln("<comment>Chunk inserts for: </comment>" . Str::plural(class_basename($class)));
        $this->command->getOutput()->progressStart($totalChunks * $chunkSize);

        $model = new $class;
        for ($chunk = 0; $chunk < $totalChunks; ++$chunk) {
            $models = [];
            for ($i = 0; $i < $chunkSize; ++$i) {
                $n = $i + ($chunkSize * $chunk) + 1;
                $factory = $class::factory();
                $data = $beforeCreateCallback ? $beforeCreateCallback($n, $chunk, $i, $factory) : [];

                if($model->hasUUID()) {
                    $data['uuid'] = SensesUUID::generate();
                }

                array_push($models, $factory->raw($data));
            }

            $class::insert($models);

            $this->command->getOutput()->progressAdvance($chunkSize);
        }
        $this->command->getOutput()->progressFinish();
    }
}
