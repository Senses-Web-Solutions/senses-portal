@extends('layouts.senses')

@section('title', 'Server '.$id)

@section('content')

<data-hydrator url="/api/v2/servers/{{ $id }}" v-slot="{ data, loading }" model="server">
    <page-header>
        <template #title>@{{ data.id }}: @{{ data.title }}</template>

        <template #actions>
            <edit-button form="ServerForm" id="{{$id}}" model="server" :data="data"></edit-button>
            <additional-options-menu :data="data" model="server" :id="{{ $id }}"></additional-options-menu>
        </template>
    </page-header>

    <div class="grid grid-cols-3 gap-8 px-8 py-8">
        <div class="col-span-2"></div>

        <div class="col-span-1">
            <basic-fields title="Server" :data="data" model="servers" :id="{{ $id }}"></basic-fields>
        </div>
    </div>
</data-hydrator>

@endsection

<!-- Generated 01-11-2023 11:27:41 -->
