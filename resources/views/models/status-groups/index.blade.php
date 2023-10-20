@extends('layouts.senses')

@section('title', 'Status Groups')

@section('content')

<page-header>
    <template #title>Status Groups</template>

    <template #actions>
        <add-button form="StatusGroupForm" model="status-group"></add-button>
    </template>
</page-header>

<page-layout>
    <status-group-table></status-group-table>
</page-layout>

@endsection

<!-- Generated 09-10-2023 12:05:02 -->
