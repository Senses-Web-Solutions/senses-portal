@extends('layouts.senses')

@section('title', 'Chats')

@section('content')

<page-header>
    <template #title>Chat Setup</template>
    <template #actions>
        <allowed-chat-site-actions></allowed-chat-site-actions>
    </template>
</page-header>

<page-layout>
    <allowed-chat-sites></allowed-chat-sites>
</page-layout>

@endsection

<!-- Generated 27-10-2023 10:55:45 -->
