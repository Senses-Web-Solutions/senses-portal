@extends('layouts.senses')

@section('title', 'Chats')

@section('content')

<page-header>
    <template #title>Chat Settings</template>
    <template #actions>
        <allowed-chat-site-actions></allowed-chat-site-actions>
    </template>
</page-header>

<page-layout>
    <company-allowed-chat-sites></company-allowed-chat-sites>
</page-layout>

@endsection

<!-- Generated 27-10-2023 10:55:45 -->
