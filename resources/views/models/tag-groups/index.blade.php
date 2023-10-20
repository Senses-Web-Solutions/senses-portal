@extends('layouts.senses')

@section('title', 'Tag Groups')

@section('content')

<page-header>
    <template #title>Tag Groups</template>

    <template #actions>
        <add-button form="TagGroupForm" model="tag-group"></add-button>
    </template>
</page-header>

<page-layout>
    <tag-group-table></tag-group-table>
</page-layout>

@endsection

<!-- Generated 09-10-2023 10:26:55 -->
