@extends('layouts.senses')

@section('title', 'Pages')

@section('content')

<page-header>
    <template #title>Pages</template>

    <template #actions>
        <add-button link="/pages/create" model="page"></add-button>
    </template>
</page-header>

<page-layout>
    <page-table></page-table>
</page-layout>

@endsection

<!-- Generated 10-10-2023 14:43:35 -->
