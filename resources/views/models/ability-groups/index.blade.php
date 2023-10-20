@extends('layouts.senses')
@section('title', 'Ability Groups')
@section('content')
<page-header>
    <template #title>Ability Groups
    </template>
    <template #actions>
        <add-button form="AbilityGroupForm" model="ability-group"></add-button>
    </template>
</page-header>
<page-layout>
    <ability-group-table></ability-group-table>
</page-layout>
@endsection

<!-- Generated 11-11-2021 08:27:41 -->
