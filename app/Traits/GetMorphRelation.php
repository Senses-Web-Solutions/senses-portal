<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

trait GetMorphRelation
{
    public function getMorphRelation($type, $id)
    {
        //todo getMorphedModel, swap to this?
        if (!isset(Relation::$morphMap[$type])) {
            return null;
        }
        $model = Relation::$morphMap[$type];
        return $model::find($id);
    }

    public function getMorphAlias($className)
    {
        $morphMapAliases = array_flip(Relation::$morphMap);
        return $morphMapAliases[$className];
    }
}
