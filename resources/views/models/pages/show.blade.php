@extends('layouts.senses')

@section('title', 'Page '.$id)

@section('content')

<data-hydrator url="/api/v2/pages/{{ $id }}" v-slot="{ data, loading }" model="page">
    <page-header>
        <template #title>@{{ data.id }}: @{{ data.title }}</template>

        <template #actions>
            <edit-button link="/pages/{{ $id }}/edit" model="page" :data="data"></edit-button>
            <additional-options-menu :data="data" model="page" :id="{{ $id }}"></additional-options-menu>
        </template>
    </page-header>

    <div class="grid grid-cols-3 gap-8 px-8 py-8">
        <div class="col-span-2"></div>

        <div class="col-span-1">
            <basic-fields title="Page" :data="data" model="pages" :id="{{ $id }}" :hidden-fields="['content']"></basic-fields>
        </div>
    </div>
</data-hydrator>

@endsection

<!-- Generated 10-10-2023 14:43:35 -->
