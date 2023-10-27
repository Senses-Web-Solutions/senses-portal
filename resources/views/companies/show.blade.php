@extends('layouts.senses')

@section('title', 'Company '.$id)

@section('content')

<data-hydrator url="/api/v2/companies/{{ $id }}" v-slot="{ data, loading }" model="company">
    <page-header>
        <template #title>@{{ data.id }}: @{{ data.title }}</template>

        <template #actions>
            <edit-button form="CompanyForm" id="{{$id}}" model="company" :data="data"></edit-button>
            <additional-options-menu :data="data" model="company" :id="{{ $id }}"></additional-options-menu>
        </template>
    </page-header>

    <div class="grid grid-cols-3 gap-8 px-8 py-8">
        <div class="col-span-2"></div>

        <div class="col-span-1">
            <basic-fields title="Company" :data="data" model="companies" :id="{{ $id }}"></basic-fields>
        </div>
    </div>
</data-hydrator>

@endsection

<!-- Generated 27-10-2023 10:55:45 -->
