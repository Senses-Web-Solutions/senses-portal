<?php

namespace App\Scopes;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

class PostgisScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $columns = $builder->getQuery()->columns;
        
        $columns = $this->replaceColumns($columns, $model);
        // dd($columns);
        $builder->getQuery()->columns = $columns;
    }

    public function replaceColumns(array|null $columns, Model $model) {

        $tableAlias = null;
        $castableGeometries = [
            strtolower(\App\Casts\Geometry::class),
            strtolower(\App\Casts\GeometryCollection::class),
            strtolower(\App\Casts\LineString::class),
            strtolower(\App\Casts\MultiLineString::class),
            strtolower(\App\Casts\MultiPoint::class),
            strtolower(\App\Casts\Point::class),
            strtolower(\App\Casts\Polygon::class),
            \App\Casts\Geometry::class,
            \App\Casts\GeometryCollection::class,
            \App\Casts\LineString::class,
            \App\Casts\MultiLineString::class,
            \App\Casts\MultiPoint::class,
            \App\Casts\Point::class,
            \App\Casts\Polygon::class,
        ];

        //hasOneOfMany seems to not be null but rather an array of ['table_name.*'], lets replace this correctly.
        if($columns && is_array($columns) && count($columns) == 1 && $columns[0] == $model->getTable().'.*') {
            $columns = null;
        } 

        if (is_null($columns)) {
            $tableAlias = $model->getTable() . '.';
            $columns = $model->getPostgisColumns();
            // $columns = array_map(fn ($v) => $tableAlias . $v, $columns); //prefix with table name
        }

        foreach ($columns as $key => $column) {
            
            $aliasColumn = $tableAlias ? Str::after($column, $tableAlias) : $column;
            if (is_string($aliasColumn)) {
                if ($model->hasCast($column, $castableGeometries)) {
                    if ($tableAlias) {
                        $column = '"' . $model->getTable() . '".' . $aliasColumn;
                    }
                    $columns[$key] = $this->postgisSQL($column, $aliasColumn);
                }
            }
            
        }

        return $columns;
    }

    public function postgisSQL(string $column, string $alias)
    {
        return DB::raw("ST_AsText($column) as $alias");
    }
}
