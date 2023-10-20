@extends('layouts.guest-page')

@section('content')


<div class="w-screen max-w-2xl mx-auto mt-16">

    <div class="p-6 bg-white rounded-lg shadow">
        <h2 class="mb-2 font-semibold">Reset Password</h2>
        <p class="mb-6">Enter your email address below, we will send you an email with a link to allow you to reset your password.</p>

        @if (session('status'))
        <secondary-alert role="alert">
            {{ session('status') }}
        </secondary-alert>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-6">
                <se-input
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    label="Email Address"
                >
                </se-input>
                @error('email')
                <se-validation-message role="alert">
                    {{ $message }}
                </se-validation-message>
                @enderror
            </div>

            <div class="flex justify-end">
                <primary-button type="submit">
                    {{ __('Send Password Reset Link') }}
                </primary-button>
            </div>
        </form>
    </div>

</div>
@endsection
