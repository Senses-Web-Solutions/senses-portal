@extends('layouts.senses')

@section('title', 'Communication Log '.$id)

@section('content')

<data-hydrator url="/api/v2/communication-logs/{{ $id }}" v-slot="{ data, loading }" model="communication-log">
    <page-header>
        <template #title>@{{ data.id }}: @{{ data.title }}</template>

        <template #actions>
            <edit-button form="CommunicationLogForm" id="{{$id}}" model="communication-log" :data="data"></edit-button>
            <additional-options-menu :data="data" model="communication-log" :id="{{ $id }}"></additional-options-menu>
        </template>
    </page-header>

    <div class="grid grid-cols-3 gap-8 px-8 py-8">
        <div class="col-span-2"></div>

        <div class="col-span-1">
            <basic-fields title="Communication Log" :data="data" model="communication-logs" :id="{{ $id }}"></basic-fields>
        </div>
    </div>
</data-hydrator>

@endsection

<!-- Generated 04-11-2023 16:09:50 -->
