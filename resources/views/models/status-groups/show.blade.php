@extends('layouts.senses')

@section('title', 'Status Group '.$id)

@section('content')

<data-hydrator url="/api/v2/status-groups/{{ $id }}" v-slot="{ data, loading }" model="status-group">
    <page-header>
        <template #title>@{{ data.id }}: @{{ data.title }}</template>

        <template #actions>
            <edit-button form="StatusGroupForm" id="{{$id}}" model="status-group" :data="data"></edit-button>
            <additional-options-menu :data="data" model="status-group" :id="{{ $id }}"></additional-options-menu>
        </template>
    </page-header>

    <div class="grid grid-cols-3 gap-8 px-8 py-8">
        <div class="col-span-2">
            <status-group-additional-information :data="data"></status-group-additional-information>
        </div>

        <div class="col-span-1">
            <basic-fields title="Status Group" :data="data" model="status-groups" :id="{{ $id }}" :hidden-fields="['locked_by']"></basic-fields>
        </div>
    </div>
</data-hydrator>

@endsection

<!-- Generated 09-10-2023 12:05:02 -->
