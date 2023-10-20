<?php

namespace App\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Kirschbaum\PowerJoins\PowerJoins;
use Spatie\QueryBuilder\AllowedFilter;
use App\Support\Filters\FiltersBetween;
use App\Support\Filters\FiltersLessThan;
use App\Support\Filters\FiltersExactOrNull;
use App\Support\Filters\FiltersGreaterThan;
use App\Support\Filters\FiltersBooleanExact;
use App\Support\Filters\FiltersIssetBoolean;
use App\Support\Filters\FiltersDateTimeExact;
use App\Support\Filters\FiltersIntegerPartial;
use App\Support\Filters\FiltersJsonTitleExact;
use App\Support\Filters\FiltersJsonTitleContains;

trait HasSensesTable
{
    use PowerJoins;

    public function mergeClientSortConfig(array $data) : array {
        return $this->mergeClientConfig($data, 'sort');
    }

    public function mergeClientFilterConfig(array $data) : array{
        return $this->mergeClientConfig($data, 'filters');
    }

    public function mergeClientFieldConfig(array $data) : array{
        return $this->mergeClientConfig($data, 'fields');
    }

    public function mergeClientEmbedConfig(array $data) : array{
        return $this->mergeClientConfig($data, 'embeds');
    }

    public function mergeClientConfig(array $data, $type) : array {
        $config = Str::slug(class_basename($this));

        $data = array_merge($data, config('client.tables.'. $config .'.'. $type, []));
        
        return $data;
    }

    public function defineFilters(array $configFilters, $config = '', array $additionalFilters = [])
    {
        $configFilters = $this->mergeClientFilterConfig($configFilters, $config);

        $config = [
            'integer' => ['contains', 'exact', 'between', 'greater_than', 'less_than'],
            'text' => ['contains', 'exact'],
            'datetime' => ['between', 'exact_datetime', 'greater_than', 'less_than'],
            'date' => ['between', 'exact', 'greater_than', 'less_than'],
            'boolean' => ['exact'],
            'json_title' => ['json_title_contains', 'json_title_exact'],
            'isset_boolean' => ['isset_boolean'],
            'exact_or_null' => ['exact_or_null'],
        ];


        $filters = [
            AllowedFilter::scope('search', internalName:'tableSearch'),
            AllowedFilter::scope('unlocked'),
            AllowedFilter::scope('format')
        ];



        foreach ($configFilters as $field => $type) {

            if($type instanceof AllowedFilter) {
                array_push($filters, $type);
                continue;
            }

            if (!isset($config[$type])) {
                continue;
            }
            
            $configTypes = $config[$type];

            foreach ($configTypes as $configType) {

                if ($configType == 'contains' && $type == 'integer') {
                    array_push($filters, AllowedFilter::custom($field, new FiltersIntegerPartial));
                }
                else if($configType == 'exact' && $type == 'boolean') {
                    array_push($filters, AllowedFilter::custom($field. '_exact', new FiltersBooleanExact, $field));
                }
                else if ($configType == 'contains') {
                    array_push($filters, $field);
                }
                else if ($configType == 'exact') {
                    array_push($filters, AllowedFilter::exact($field . '_exact', $field));
                }
                else if ($configType == 'exact_datetime') {
                    array_push($filters, AllowedFilter::custom($field . '_exact', new FiltersDateTimeExact, $field));
                }
                else if ($configType == 'between') {
                    array_push($filters, AllowedFilter::custom($field . '_between', new FiltersBetween, $field));
                }
                else if ($configType == 'greater_than') {
                    array_push($filters, AllowedFilter::custom($field . '_greater_than', new FiltersGreaterThan, $field));
                }
                else if ($configType == 'exact_or_null') {
                    array_push($filters, AllowedFilter::custom($field . '_exact_or_null', new FiltersExactOrNull, $field));
                }
                else if ($configType == 'less_than') {
                    array_push($filters, AllowedFilter::custom($field . '_less_than', new FiltersLessThan, $field));
                }
                // else if ($configType == 'json_title_exact') {
                //     array_push($filters, AllowedFilter::exact($field . '_exact', $field.'->title'));
                // }
                // else if ($configType == 'json_title_contains') {
                //     array_push($filters, $field.'->title');
                // }
                else if ($configType == 'json_title_exact') {
                    array_push($filters, AllowedFilter::custom($field . '_exact', new FiltersJsonTitleExact, $field));
                }
                else if ($configType == 'json_title_contains') {
                    array_push($filters, AllowedFilter::custom($field, new FiltersJsonTitleContains, $field));
                }
                else if ($configType == 'isset_boolean') {
                    array_push($filters, AllowedFilter::custom($field. '_isset_boolean', new FiltersIssetBoolean, $field));
                }
            }
        }

        $filters = array_merge($filters, $additionalFilters);
        
        return $filters;
    }

    public function tableColumnSearch(&$query, string $search, array $relations) {
        // These only apply when using fields in the url, as the relation will be autoloaded as part of the query
        $requestFields = request()->input('fields', []);

        if(empty($requestFields)) {
            return;
        }

        foreach($relations as $relation => $relationFields) {
            if(!isset($requestFields[$relation])) {
                continue;
            }
            $relationFields = Arr::wrap($relationFields);
            $fields = explode(',', $requestFields[$relation]);
            foreach($relationFields as $relationField) {
                if(in_array($relationField, $fields)) {
                    $query->orWhere($relation . '.' . $relationField, 'ilike', '%'. $search.'%');
                }
            }
        }
    }
}
