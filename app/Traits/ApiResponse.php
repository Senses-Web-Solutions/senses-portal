<?php

namespace App\Traits;

use Throwable;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait ApiResponse
{
    protected $exceptionMessages = [
        NotFoundHttpException::class => 'Not found'
    ];

    public function respond($data, $resourceClass = null)
    {
        $status = 200;
        $responseData = $data;
        if($data instanceof Model || $data instanceof User) {
            $status = $data->wasRecentlyCreated ? 201 : 200;
        }

        if (is_string($data)) {
            $responseData = [
                'object' => 'action',
                'message' => $data
            ];
        }

        return response()->json($responseData, $status);
    }

    public function respondDeleted($item)
    {
        $name = ucfirst(str_replace('_', ' ', Str::snake(class_basename($item))));
        return $this->respond("{$name} has been deleted.");
    }

    public function restrictFields(JsonResponse $jsonResponse, array $abilityFields) {
		$user = getCurrentUser();
		$data = $jsonResponse->getData(true);
		$dirty = false;

		foreach($abilityFields as $ability => $fields) {
			if(!empty(array_intersect(Arr::wrap($fields), array_keys($data)))) {
				if(!$user->can($ability)) {
					$data = Arr::except($data, $fields);
					$dirty = true;
				}
			}
		}

		if($dirty) {
			$jsonResponse->setData($data);
		}

		return $jsonResponse;
	}

    //todo ensure status codes and nicer errors come back, 404 -> show what model is missing etc
    // public function respondWithError(Throwable $e)
    // {
    //     $message = $e->getMessage();
    //     if ($message == '') {
    //         $message = isset($this->exceptionMessages[get_class($e)]) ? $this->exceptionMessages[get_class($e)] : "Unknown error has occurred";
    //     }

    //     return response()->json([
    //         'object' => 'error',
    //         'message' => $e->getMessage(),
    //             'exception' => get_class($e),
    //             'file' => $e->getFile(),
    //             'line' => $e->getLine(),
    //             'trace' => collect($e->getTrace())->map(function ($trace) {
    //                 return Arr::except($trace, ['args']);
    //             })->all()
    //         ], 404);
    // }
}
