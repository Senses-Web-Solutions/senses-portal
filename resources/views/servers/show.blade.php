@extends('layouts.senses')

@section('title', 'Server '.$id)

@section('content')

<data-hydrator url="/api/v2/servers/{{ $id }}" v-slot="{ data, loading }" model="server">
    <page-header>
        <template #title>@{{ data.title }}</template>

        <template #actions>
            <edit-button form="ServerForm" id="{{$id}}" model="server" :data="data"></edit-button>
            <additional-options-menu :data="data" model="server" :id="{{ $id }}"></additional-options-menu>
        </template>
    </page-header>

    <server-dashboard :server="data"></server-dashboard>
</data-hydrator>

@endsection

<!-- Generated 01-11-2023 11:27:41 -->
