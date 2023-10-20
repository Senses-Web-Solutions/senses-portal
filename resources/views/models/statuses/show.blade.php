@extends('layouts.senses')

@section('title', 'Status '.$id)

@section('content')

<data-hydrator url="/api/v2/statuses/{{ $id }}" v-slot="{ data, loading }" model="status">
    <page-header>
        <template #title>@{{ data.id }}: @{{ data.title }}</template>

        <template #actions>
            <edit-button form="StatusForm" id="{{$id}}" model="status" :data="data"></edit-button>
            <additional-options-menu :data="data" model="status" :id="{{ $id }}"></additional-options-menu>
        </template>
    </page-header>

    <div class="grid grid-cols-3 gap-8 px-8 py-8">
        <div class="col-span-2"></div>

        <div class="col-span-1">
            <basic-fields title="Status" :data="data" model="statuses" :id="{{ $id }}"></basic-fields>
        </div>
    </div>
</data-hydrator>

@endsection

<!-- Generated 09-10-2023 12:35:29 -->
