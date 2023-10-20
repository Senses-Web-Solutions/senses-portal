@extends('layouts.senses')

@section('title', 'Tag Group '.$id)

@section('content')

<data-hydrator url="/api/v2/tag-groups/{{ $id }}" v-slot="{ data, loading }" model="tag-group">
    <page-header>
        <template #title>@{{ data.id }}: @{{ data.title }}</template>

        <template #actions>
            <edit-button form="TagGroupForm" id="{{$id}}" model="tag-group" :data="data"></edit-button>
            <additional-options-menu :data="data" model="tag-group" :id="{{ $id }}"></additional-options-menu>
        </template>
    </page-header>

    <div class="grid grid-cols-3 gap-8 px-8 py-8">
        <div class="col-span-2">
            <tag-group-additional-information :data="data"></tag-group-additional-information>
        </div>

        <div class="col-span-1">
            <basic-fields title="Tag Group" :data="data" model="tag-groups" :id="{{ $id }}"></basic-fields>
        </div>
    </div>
</data-hydrator>

@endsection

<!-- Generated 09-10-2023 10:26:55 -->
