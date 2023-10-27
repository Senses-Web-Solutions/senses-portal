@extends('layouts.senses')

@section('title', 'Companies')

@section('content')

<page-header>
    <template #title>Companies</template>

    <template #actions>
        <add-button form="CompanyForm" model="company"></add-button>
    </template>
</page-header>

<page-layout>
    <company-table></company-table>
</page-layout>

@endsection

<!-- Generated 27-10-2023 10:55:45 -->
