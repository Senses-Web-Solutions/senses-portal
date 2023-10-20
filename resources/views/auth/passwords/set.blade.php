@extends('layouts.guest-page')

@section('content')
    <div class="w-screen max-w-2xl mx-auto mt-16">

        <div class="p-6 bg-white rounded-lg shadow">
            <h2 class="mb-2 font-semibold">Set Password</h2>
            <p class="mb-6">Enter your email address and set your new password below.</p>

            @if (session('status'))
                <secondary-alert role="alert">
                    {{ session('status') }}
                </secondary-alert>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ request()->route('token') }}">

                <div>
                    <label for="email" class="block font-medium leading-5 text-zinc-700">
                        Email Address
                    </label>
                    <div class="mt-1 rounded-md shadow-sm">
                        <input id="email" name="email" type="email" value="{{ request()->input('email') }}"
                            autocomplete="email" autofocus placeholder="Email" required
                            class="appearance-none block w-full px-3 py-2 border border-zinc-300 rounded-md placeholder-zinc-400 focus:outline-none focus:ring-blue focus:border-primary-300 transition duration-150 ease-in-out sm:sm:leading-5 @if($errors->updatePassword->get('email')) border-red-500 @endif">
                            </div>
                            @if ($errors->updatePassword->get('email'))
                            <ul class="text-sm text-red-500">
                                @foreach ($errors->updatePassword->get('email') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            @endif
                        </div>

                        <div class="
                            mt-4">
                        <label for="
                                                password" class="block font-medium leading-5 text-zinc-700">
                            {{ __('Password') }}
                        </label>
                        <div class="mt-1 rounded-md shadow-sm">
                            <input id="password" name="password" type="password" value="{{ old('password') }}"
                                autocomplete="password" autofocus placeholder="Password" required
                                class="appearance-none block w-full px-3 py-2 border border-zinc-300 rounded-md placeholder-zinc-400 focus:outline-none focus:ring-blue focus:border-primary-300 transition duration-150 ease-in-out sm:sm:leading-5 @if($errors->updatePassword->get('password')) border-red-500 @endif">
                            </div>
                            @if ($errors->updatePassword->get('password'))
                            <ul class="text-sm text-red-500">
                                @foreach ($errors->updatePassword->get('password') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            @endif
                        </div>

                        <div class="
                                mt-4">
                            <label for="password_confirmation" class="block font-medium leading-5 text-zinc-700">
                                {{ __('Confirm Password') }}
                            </label>
                            <div class="mt-1 rounded-md shadow-sm">
                                <input id="password_confirmation" name="password_confirmation" type="password"
                                    value="{{ old('password_confirmation') }}" autocomplete="password_confirmation"
                                    autofocus placeholder="Confirm Password" required
                                    class="appearance-none block w-full px-3 py-2 border border-zinc-300 rounded-md placeholder-zinc-400 focus:outline-none focus:ring-blue focus:border-primary-300 transition duration-150 ease-in-out sm:sm:leading-5 @if($errors->updatePassword->get('password')) border-red-500 @endif">
                            </div>
                            @if ($errors->updatePassword->get('password_confirmation'))
                            <ul class="text-md text-red-500">
                                @foreach ($errors->updatePassword->get('password_confirmation') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            @endif
                        </div>

                        <div class="
                                flex justify-between">
                            <input type="submit"
                                class="inline-flex items-center justify-center block ml-auto px-4 py-2 mt-6 font-medium leading-5 text-white transition duration-150 ease-in-out border border-transparent rounded-md focus:outline-none focus:ring-indigo bg-primary-600 hover:bg-primary-500 focus:border-primary-700 active:bg-primary-700"
                                value="{{ __('Set Password') }}">
                        </div>
            </form>
        </div>
    </div>
    </div>
@endsection
