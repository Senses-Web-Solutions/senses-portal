@extends('layouts.senses')

@section('title', 'Statuses')

@section('content')

<page-header>
    <template #title>Statuses</template>

    <template #actions>
        <add-button form="StatusForm" model="status"></add-button>
    </template>
</page-header>

<page-layout>
    <status-table></status-table>
</page-layout>

@endsection

<!-- Generated 09-10-2023 12:35:29 -->
