@extends('layouts.senses')

@section('title', 'Chats')

@section('content')

<page-header>
    <template #title>Inbox</template>

    <template #actions>
        {{-- <chat-actions></chat-actions> --}}
    </template>
</page-header>

<page-layout flush>
    <chat-inbox></chat-inbox>
</page-layout>

@endsection

<!-- Generated 27-10-2023 10:55:45 -->
