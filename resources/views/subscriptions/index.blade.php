@extends('layouts.senses')

@section('title', 'Subscriptions')

@section('content')

<page-header>
    <template #title>Subscriptions</template>

    <template #actions>
        <add-button form="SubscriptionForm" model="subscription"></add-button>
    </template>
</page-header>

<page-layout>
    <subscription-table></subscription-table>
</page-layout>

@endsection

<!-- Generated 04-11-2023 16:09:38 -->
