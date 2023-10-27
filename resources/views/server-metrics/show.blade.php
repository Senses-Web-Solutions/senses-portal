@extends('layouts.senses')

@section('title', 'Server Metric '.$id)

@section('content')

<data-hydrator url="/api/v2/server-metrics/{{ $id }}" v-slot="{ data, loading }" model="server-metric">
    <page-header>
        <template #title>@{{ data.id }}: @{{ data.title }}</template>

        <template #actions>
            <edit-button form="ServerMetricForm" id="{{$id}}" model="server-metric" :data="data"></edit-button>
            <additional-options-menu :data="data" model="server-metric" :id="{{ $id }}"></additional-options-menu>
        </template>
    </page-header>

    <div class="grid grid-cols-3 gap-8 px-8 py-8">
        <div class="col-span-2"></div>

        <div class="col-span-1">
            <basic-fields title="Server Metric" :data="data" model="server-metrics" :id="{{ $id }}"></basic-fields>
        </div>
    </div>
</data-hydrator>

@endsection

<!-- Generated 27-10-2023 10:55:27 -->
