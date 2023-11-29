@extends('layouts.senses')

@section('title', 'Servers')

@section('content')

<page-header>
    <template #title>Servers</template>

    <template #actions>
        <add-button form="ServerForm" model="server"></add-button>
    </template>
</page-header>

<page-layout :flush="true">
    <server-list></server-list>
</page-layout>

@endsection

<!-- Generated 01-11-2023 11:27:41 -->
