@extends('layouts.senses')

@section('title', 'Tag '.$id)

@section('content')

<data-hydrator url="/api/v2/tags/{{ $id }}" v-slot="{ data, loading }" model="tag">
    <page-header>
        <template #title>@{{ data.id }}: @{{ data.title }}</template>

        <template #actions>
            <edit-button form="TagForm" id="{{$id}}" model="tag" :data="data"></edit-button>
            <additional-options-menu :data="data" model="tag" :id="{{ $id }}"></additional-options-menu>
        </template>
    </page-header>

    <div class="grid grid-cols-3 gap-8 px-8 py-8">
        <div class="col-span-2"></div>

        <div class="col-span-1">
            <basic-fields title="Tag" :data="data" model="tags" :id="{{ $id }}"></basic-fields>
        </div>
    </div>
</data-hydrator>

@endsection

<!-- Generated 09-10-2023 10:18:19 -->
