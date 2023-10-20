@extends('layouts.senses')

@section('title', 'Ability Group '.$id)
@section('content')

    <data-hydrator url="/api/v2/ability-groups/{{ $id }}" v-slot="{ data, loading }" model="ability-group">
        <page-header>
            <template #title>@{{ data.id }}: @{{ data.title }}</template>
            {{-- <template #subtitle>@{{ data.title }}</template> --}}
            <template #actions>
                <edit-button form="AbilityGroupForm" id="{{$id}}" model="ability-group" :data="data"></edit-button>
                <additional-options-menu :data="data" model="ability-group" :id="{{ $id }}"></additional-options-menu>
            </template>
        </page-header>

        <div class="grid grid-cols-3 gap-8 px-8 py-8">
            <div class="col-span-2">
                <ability-group-abilities :data="data"></ability-group-abilities>
            </div>
            <div class="col-span-1">
                <basic-fields title="Ability Group" model="ability-groups" :id="{{ $id }}" :data="data" :hidden-fields="['abilities']"></basic-fields>
            </div>
        </div>

    </data-hydrator>

@endsection

<!-- Generated 11-11-2021 08:27:41 -->
