@extends('layouts.senses')

@section('title', 'Chat Users '.$id)

@section('content')

<data-hydrator url="/api/v2/chat-users/{{ $id }}" v-slot="{ data, loading }" model="chat-user">
    <page-header>
        <template #title>@{{ data.id }}: @{{ data.full_name }}</template>

        <template #actions>
            <edit-button form="ChatUserForm" id="{{$id}}" model="chat-user" :data="data"></edit-button>
            <additional-options-menu :data="data" model="chat-user" :id="{{ $id }}"></additional-options-menu>
        </template>
    </page-header>

    <page-layout>
        <chat-user-show :data="data" :id="{{ $id }}"></chat-user-show>
    </page-layout>
</data-hydrator>

@endsection

<!-- Generated 27-10-2023 10:55:45 -->
