@extends('layouts.senses')

@section('title', 'Page')

@section('content')

<page-builder @isset($id) :id="{{ $id }}" @endif></page-builder>


@endsection
