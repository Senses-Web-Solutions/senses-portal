@extends('layouts.senses')

@section('title', 'Communication Logs')

@section('content')

<page-header>
    <template #title>Communication Logs</template>

    <template #actions>
        <add-button form="CommunicationLogForm" model="communication-log"></add-button>
    </template>
</page-header>

<page-layout>
    <communication-log-table></communication-log-table>
</page-layout>

@endsection

<!-- Generated 04-11-2023 16:09:50 -->
