@extends('layouts.senses')

@section('title', 'User '.$id)

@section('content')

<data-hydrator url="/api/v2/users/{{ $id }}" v-slot="{ data, loading }" model="user">
    <page-header>
        <template #title>@{{ data.id }}: @{{ data.full_name }}</template>

        <template #actions>
            <edit-button form="UserForm" id="{{$id}}" model="user" :data="data"></edit-button>
            <additional-options-menu :data="data" model="user" :id="{{ $id }}"></additional-options-menu>
        </template>
    </page-header>

    <div class="grid grid-cols-3 gap-8 px-8 py-8">
        <div class="col-span-2">
            {{-- Canned messages --}}
            <user-canned-message-table />

            {{-- Default Message --}}
            {{-- <UserDefaultMessageTable /> --}}
        </div>

        <div class="col-span-1">
            <basic-fields title="User" :data="data" model="users" :id="{{ $id }}" :hidden-fields="['locked_by']"></basic-fields>
        </div>
    </div>
</data-hydrator>

@endsection

<!-- Generated 10-10-2023 10:05:13 -->
