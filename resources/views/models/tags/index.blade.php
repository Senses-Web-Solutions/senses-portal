@extends('layouts.senses')

@section('title', 'Tags')

@section('content')

<page-header>
    <template #title>Tags</template>

    <template #actions>
        <add-button form="TagForm" model="tag"></add-button>
    </template>
</page-header>

<page-layout>
    <tag-table></tag-table>
</page-layout>

@endsection

<!-- Generated 09-10-2023 10:18:19 -->
