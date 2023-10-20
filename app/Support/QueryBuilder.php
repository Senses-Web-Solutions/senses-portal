<?php
namespace App\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use App\Exceptions\InvalidAppendQuery;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;

class QueryBuilder extends \Spatie\QueryBuilder\QueryBuilder
{
    protected $allowedAppends;
    protected $appends;

    public function allowedAppends($appends): self
    {
        $appends = is_array($appends) ? $appends : func_get_args();

        $this->allowedAppends = collect($appends);

        $this->ensureAllAppendsExist();

        return $this;
    }

    protected function addAppendsToResults(Collection $results)
    {
        return $results->each(function (Model $result) {
            return $result->append($this->request->appends()->toArray());
        });
    }

    protected function addAppendsToCursor($results)
    {
        return $results->each(function (Model $result) {
            return $result->append($this->request->appends()->toArray());
        });
    }

    protected function ensureAllAppendsExist()
    {
        $appends = $this->request->appends();

        $diff = $appends->diff($this->allowedAppends);

        if ($diff->count()) {
            throw InvalidAppendQuery::appendsNotAllowed($diff, $this->allowedAppends);
        }
    }

    public function get($columns = ['*'])
    {
        $result = parent::get($columns);

        if ($this->appends && count($this->appends) > 0) {
            $result = $this->setAppendsToResult($result);
        }

        return $result;
    }

    /**
     * Create a new QueryBuilder for a request and model.
     *
     * @param string|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation $baseQuery Model class or base query builder
     * @param \Illuminate\Http\Request $request
     * @param bool $useModelOptions
     *
     * @return \Spatie\QueryBuilder\QueryBuilder
     */
    public static function for($subject, ?Request $request = null, $useModelOptions = true): static
    {

        if(!$useModelOptions) {
            return parent::for($subject, $request);
        }

        $query = parent::for($subject, $request)->restrictModelColumns()->modelDefaults()->applyAutoJoins();
        // $query->dd();
        return $query;
    }

    // public function requestLimit() {
    //     $requestLimit = $this->request->limit;
    //     if ($requestLimit) {
    //         $queryBuilderLimit = $this->getEloquentBuilder()->getQuery()->limit;
    //         if (!$queryBuilderLimit || $queryBuilderLimit > $requestLimit) {
    //             $this->getEloquentBuilder()->getQuery()->limit = $requestLimit;
    //         }
    //     }
    //     return $this;
    // }

    public function restrictModelColumns(?Request $request = null) {
        //check for certain permissions, if user has fields check against fields.
        //if user has all and there are restricted fields reject.
        $request = $request  ?? request();
        $requestFields = $this->request->fields();
        $parentModel = $this->getEloquentBuilder()->getModel();
        $parentModelTable = $parentModel->getTable();
        $modelFields = [];

        foreach($requestFields as $table => $fields) {
            if($table == $parentModelTable) {
                $modelFields[$parentModel::class] = $fields;
            }
            else {

                $relatedModel = $parentModel;
                $relations = explode('.', $table);
                foreach($relations as $relation) {
                    $relatedModel = optional($relatedModel->$relation())->getRelated();
                }

                if($relatedModel) {
                    $modelFields[$relatedModel::class] = $fields;
                }
            }
        }

        if($requestFields->isEmpty()) {
            $modelFields[$parentModel::class] = [];
        }

        foreach($modelFields as $model => $fields) {
            $model = app($model);
            if(method_exists($model, 'restrictFields')) {

                if(!$model->restrictFields(getCurrentUser(), $fields)) {
                    abort(403, 'You do not have permission to view this data on ' . $model->getMorphClass());
                }
            }
        }
        // dd('ok', $requestFields, $modelFields);
// dd($fields, $modelFields);
// dd($this->request->fields());

        //if empty, check against base model for all


        return $this;
    }

    public function modelDefaults() {
        $model = $this->getEloquentBuilder()->getModel();

        $allowedFilters = method_exists($model, 'allowedFilters') ? $model->allowedFilters() : [];
        $allowedIncludes = method_exists($model, 'allowedEmbeds') ? $model->allowedEmbeds() : [];
        $allowedFields = method_exists($model, 'allowedFields') ? $model->allowedFields() : null;
        $allowedSorts = method_exists($model, 'allowedSorts') ? $model->allowedSorts() : [];
        $allowedAppends = method_exists($model, 'allowedAppends') ? $model->allowedAppends() : [];

        $defaultSort = method_exists($model, 'defaultSort') ? $model->defaultSort() : "-{$model->getKeyName()}";

        if($allowedFields) {
            $this->allowedFields($allowedFields);
        }
        return $this->allowedSorts($allowedSorts)->defaultSort($defaultSort)->allowedAppends($allowedAppends)->allowedFilters($allowedFilters)->allowedIncludes($allowedIncludes);
    }

    public function applyAutoJoins() {
        //! must be called after calling spatie functions ->getModelDefaults(), so selects apply correctly.

        $model = $this->getEloquentBuilder()->getModel();
        $joins = [];

        //auto detect joins from filters (doesn't quite support nested)
        $this->allowedFilters->each(function (AllowedFilter $filter) use(&$joins) {
            if($this->isFilterRequested($filter) && $this->isFilterRelational($filter)) {

                $relation = Str::beforeLast($filter->getName(), '.');
                $field = Str::afterLast($filter->getName(), '.');
                if(!isset($joins[$relation])) {
                    $joins[$relation] = [];
                }

                array_push($joins[$relation], $field);
            }
        });

        //auto detect joins from requested fields
        $requestedFields = $this->request->fields();
        unset($requestedFields[$model->getTable()]);    //remove current query table from join detection.

        foreach($requestedFields as $relation => $fields){
            if(!isset($joins[$relation])) {
                $joins[$relation] = [];
            }
            $joins[$relation] = array_merge( $joins[$relation], $fields);
        }

        //ensure unique joins
        foreach($joins as $relation => $fields) {
            $joins[$relation] = array_unique($joins[$relation]);
        }


        //apply auto joins, selecting using snake_case for columns and aliasing joins to relation name
        foreach($joins as $joinRelation => $joinColumns) {
            foreach($joinColumns as $joinColumn) {

                //resolve nested relations model
                $joinTable = null;
                $relationParts = explode('.', $joinRelation);

                if(count($relationParts) == 1) {

                    if(str($joinColumn)->endsWith('_exact')) {
                        $joinColumn = str($joinColumn)->beforeLast('_exact');
                    }
                    else if(str($joinColumn)->endsWith('_between')) {
                        $joinColumn = str($joinColumn)->beforeLast('_between');
                    }
                    else if(str($joinColumn)->endsWith('_greater_than')) {
                        $joinColumn = str($joinColumn)->beforeLast('_greater_than');
                    }
                    else if(str($joinColumn)->endsWith('_less_than')) {
                        $joinColumn = str($joinColumn)->beforeLast('_less_than');
                    }

                    $joinSelectColumn = $relationParts[0] .'.'.$joinColumn;
                    $joinColumnAlias = str_replace('.','__', $joinSelectColumn);
                    $this->getEloquentBuilder()->addSelect($joinSelectColumn . ' as ' . $joinColumnAlias);
                    continue;
                }

                $relation = $model;
                foreach($relationParts as $relationPart) {
                    $relation = $relation->$relationPart()->getRelated();
                }

                $joinTable = $relation->getTable();
                $joinColumnAlias = str_replace('.','__', $joinRelation . '.'. $joinColumn);
                $joinSelectColumn = $joinTable .'.'.$joinColumn;

                // dd('select '. $joinSelectColumn . ' as ' . $joinColumnAlias);

                $this->getEloquentBuilder()->addSelect($joinSelectColumn . ' as ' . $joinColumnAlias);
            }

            // dd($joinRelation, $joinColumns);
            // $this->getEloquentBuilder()->leftJoinRelationship($joinRelation, $joinRelation);
            $this->getEloquentBuilder()->leftJoinRelationshipUsingAlias($joinRelation, $joinRelation);
            // $this->getEloquentBuilder()->leftJoinRelationship($joinRelation, function($j) use(&$joinRelation){
            //     $j->as($joinRelation);
            // });
        }

        return $this;
    }

    public function isFilterRelational(AllowedFilter $filter) {
        return str_contains($filter->getName(), '.');
    }

    public function __call($name, $arguments) {
        $result = parent::__call($name, $arguments);

        if ($result instanceof Model) {
            $this->addAppendsToResults(collect([$result]));
        }

        if ($result instanceof Collection) {
            $this->addAppendsToResults($result);
        }

        if ($result instanceof LengthAwarePaginator || $result instanceof Paginator || $result instanceof CursorPaginator) {
            $this->addAppendsToResults(collect($result->items()));
        }

        return $result;
    }

    protected function initializeRequest(?Request $request = null): static
    {
        parent::initializeRequest($request);
// dd(';');

        $model = $this->getEloquentBuilder()->getModel();
        $table = $model->getTable();

        if($this->request->has('group_by')) {
            $groupBy = $this->request->get('group_by');
            $field = Str::afterLast($groupBy, '.');
            $relations = Str::beforeLast($groupBy, '.');
            if(!str_contains($groupBy, '.')) {
                $relations = $table;
            }
            $this->request->query->remove('fields');
            $this->request->query->remove('sort');
            $this->request->query->add(['sort' => $this->request->get('group_by')]);
            $fields = [];
            Arr::set($fields, $relations, $field);
            $this->request->query->add(['fields' => $fields ]);
        }

       return $this;;
    }

}
