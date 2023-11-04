@extends('layouts.senses')

@section('title', 'Revenues')

@section('content')

<page-header>
    <template #title>Revenues</template>

    <template #actions>
        <add-button form="RevenueForm" model="revenue"></add-button>
    </template>
</page-header>

<page-layout>
    <revenue-table></revenue-table>
</page-layout>

@endsection

<!-- Generated 04-11-2023 16:09:26 -->
