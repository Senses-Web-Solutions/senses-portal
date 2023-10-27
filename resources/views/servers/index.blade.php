@extends('layouts.senses')

@section('title', 'Servers')

@section('content')

<page-header>
    <template #title>Servers</template>

    <template #actions>
        <add-button form="ServerForm" model="server"></add-button>
    </template>
</page-header>

<page-layout>
    <server-table></server-table>
</page-layout>

@endsection

<!-- Generated 27-10-2023 10:53:42 -->
