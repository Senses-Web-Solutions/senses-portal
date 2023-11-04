@extends('layouts.senses')

@section('title', 'Subscription '.$id)

@section('content')

<data-hydrator url="/api/v2/subscriptions/{{ $id }}" v-slot="{ data, loading }" model="subscription">
    <page-header>
        <template #title>@{{ data.id }}: @{{ data.title }}</template>

        <template #actions>
            <edit-button form="SubscriptionForm" id="{{$id}}" model="subscription" :data="data"></edit-button>
            <additional-options-menu :data="data" model="subscription" :id="{{ $id }}"></additional-options-menu>
        </template>
    </page-header>

    <div class="grid grid-cols-3 gap-8 px-8 py-8">
        <div class="col-span-2"></div>

        <div class="col-span-1">
            <basic-fields title="Subscription" :data="data" model="subscriptions" :id="{{ $id }}"></basic-fields>
        </div>
    </div>
</data-hydrator>

@endsection

<!-- Generated 04-11-2023 16:09:38 -->
