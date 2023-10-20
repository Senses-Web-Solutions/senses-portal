@extends('layouts.senses')

@section('title', 'Block Group '.$id)

@section('content')

<data-hydrator url="/api/v2/block-groups/{{ $id }}" v-slot="{ data, loading }" model="block-group">
    <page-header>
        <template #title>@{{ data.id }}: @{{ data.title }}</template>

        <template #actions>
            <edit-button form="BlockGroupForm" id="{{$id}}" model="block-group" :data="data"></edit-button>
            <additional-options-menu :data="data" model="block-group" :id="{{ $id }}"></additional-options-menu>
        </template>
    </page-header>

    <div class="grid grid-cols-3 gap-8 px-8 py-8">
        <div class="col-span-2"></div>

        <div class="col-span-1">
            <basic-fields title="Block Group" :data="data" model="block-groups" :id="{{ $id }}"></basic-fields>
        </div>
    </div>
</data-hydrator>

@endsection

<!-- Generated 16-10-2023 10:39:10 -->
