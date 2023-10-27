@extends('layouts.senses')

@section('title', 'Server Metrics')

@section('content')

<page-header>
    <template #title>Server Metrics</template>

    <template #actions>
        <add-button form="ServerMetricForm" model="server-metric"></add-button>
    </template>
</page-header>

<page-layout>
    <server-metric-table></server-metric-table>
</page-layout>

@endsection

<!-- Generated 27-10-2023 10:55:27 -->
