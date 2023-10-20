@extends('layouts.senses')

@section('title', 'Block Groups')

@section('content')

<page-header>
    <template #title>Block Groups</template>

    <template #actions>
        <add-button form="BlockGroupForm" model="block-group"></add-button>
    </template>
</page-header>

<page-layout>
    <block-group-table></block-group-table>
</page-layout>

@endsection

<!-- Generated 16-10-2023 10:39:10 -->
