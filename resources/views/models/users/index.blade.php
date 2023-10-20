@extends('layouts.senses')

@section('title', 'Users')

@section('content')

<page-header>
    <template #title>Users</template>

    <template #actions>
        <add-button form="UserForm" model="user"></add-button>
    </template>
</page-header>

<page-layout>
    <user-table></user-table>
</page-layout>

@endsection

<!-- Generated 10-10-2023 10:05:12 -->
