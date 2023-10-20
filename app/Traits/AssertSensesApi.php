<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\DatabaseTransactions;

Trait AssertSensesApi {

    public function setupSensesAssert(string $modelClass) {
        //todo create user with correct requests for envs that have a blank db
        //create a user with specific role
        // $user = User::factory()->create();
        $abilityName = Str::kebab(Str::singular(class_basename($modelClass)));
        $user = signInPassportUser();
        $user->allow(['create-' . $abilityName, 'update-' .$abilityName, 'show-' . $abilityName, 'list-' .$abilityName, 'delete-' . $abilityName]);

        Queue::fake();
    }

    public function assertListApi(string $modelClass, string $url, array $attributes = []) {
        $this->setupSensesAssert($modelClass);

        $response = $this->getJson($url);
        $response->assertSuccessful();

        //todo this is a very basic test
    }

    public function assertShowApi(string $modelClass, string $url, array $attributes = []) {
        $this->setupSensesAssert($modelClass);

        $model = $modelClass::factory()->create($attributes);
        $model->setRelations([]);

        $response = $this->getJson($url .'/'. $model->id);
        $response->assertSuccessful();
        $response->assertJson($model->toArray());
    }

    public function assertCreateApi(string $modelClass, string $url, array $attributes = []) {
        $this->setupSensesAssert($modelClass);

        $attributes = $this->resolveAttributes($modelClass, $attributes);
        $response = $this->postJson($url, $attributes);
        $response->assertSuccessful();
        $response->assertJson($attributes);

        $this->assertDatabaseHas(app($modelClass)->getTable(), array_merge($attributes, [
            'id' => $response['id'],
        ]));
    }

    public function assertUpdateApi(string $modelClass, string $url, array $attributes = []) {
        $this->setupSensesAssert($modelClass);

        $model = $modelClass::factory()->create($attributes);
        $model->setRelations([]);

        $attributes = $this->resolveAttributes($modelClass, $attributes);
        $response = $this->putJson($url .'/'. $model->id, $attributes);
        $response->assertSuccessful();
        $response->assertJson($attributes);

        $this->assertDatabaseHas($model->getTable(), array_merge($attributes, [
            'id' => $model->id,
        ]));
    }

    public function assertDestroyApi(string $modelClass, string $url, array $attributes = []) {
        $this->setupSensesAssert($modelClass);
        $model = $modelClass::factory()->create($attributes);
        $model->setRelations([]);

        $response = $this->deleteJson($url .'/'.$model->id);
        $response->assertSuccessful();
        $this->assertSoftDeleted($model);
    }

    public function resolveAttributes(string $modelClass, array $attributes = []) {
        $model = $modelClass::factory()->make($attributes);
        return $model->toArray();
    }
}
