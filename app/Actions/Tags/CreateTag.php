<?php

namespace App\Actions\Tags;

use App\Models\Tag;
use App\Models\TagGroup;
use Illuminate\Support\Str;
use Spatie\QueueableAction\QueueableAction;

class CreateTag
{
    use QueueableAction;

    public function execute(array $data)
    {
        if (isset($data['slug'])) {
            $tag = Tag::where('slug', $data['slug'])->first();
            if ($tag) {
                return $tag;
            }
        }

        $tag = new Tag($data);

        $tag->slug = Str::slug($tag->title);

        $tag->save();

        $tagGroups = $data['tag_group_ids'] ?? [];
        if(isset($data['tag_group_slugs'])) {
            $tagGroups = array_merge($tagGroups, TagGroup::whereIn('slug', $data['tag_group_slugs'])->pluck('id')->toArray());
        }
        $tag->tagGroups()->sync($tagGroups);

        return $tag;
    }
}

//Generated 13-10-2021 11:49:44
