@extends('layouts.guest')

@section('body-class', 'login')

@section('content')

<div class="h-screen w-screen flex items-center justify-center">
    <div class="-mt-48 sm:mx-auto sm:w-full" style="width: 450px">
        <div class="flex justify-center mb-6">
            <img class="h-20 w-auto" src="/images/logo.svg" alt="{{ config('app.name')}} Logo">
            <p class="text-4xl text-zinc-900 font-light my-auto pl-4 pt-4">{{ config('app.name')}}</p>
        </div>
        <h2 class="mt-5 text-center text-4xl leading-6 text-zinc-900 mb-3">Sign in to your account</h2>
        <p class="mt-2 text-center text-base text-zinc-600">
            Please enter your username and password
        </p>

        <div class="mt-8 bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <form action="{{ route('login') }}" method="POST" autocomplete="on" data-bitwarden-watching="1">
                @csrf

                <div>
                    <label for="email" class="block text-base font-medium leading-5 text-zinc-700">
                        Email address
                    </label>
                    <div class="mt-1 rounded-md shadow-sm">
                        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" autofocus placeholder="Email" required
                            class="appearance-none block w-full px-3 py-2 border border-zinc-300 rounded-md placeholder-zinc-400 focus:outline-none focus:ring-primary focus:border-primary-300 transition duration-150 ease-in-out sm:sm:leading-5 @error('email') form-input-error @endif">
                    </div>
                    @error('email')
                        <p class="text-base text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6">
                    <label for="password" class="block text-base font-medium leading-5 text-zinc-700">
                        Password
                    </label>
                    <div class="mt-1 rounded-md shadow-sm">
                        <input id="password" name="password" type="password" required autocomplete="current-password" autofocus placeholder="******************"
                            class="appearance-none block w-full px-3 py-2 border border-zinc-300 rounded-md placeholder-zinc-400 focus:outline-none focus:ring-primary focus:border-primary-300 transition duration-150 ease-in-out sm:sm:leading-5 @error('password') form-input-error @endif">
                    </div>
                    @error('password')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between mt-6">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox" checked value="{{ old('remember-me') ? 'checked' : '' }}" class="w-4 h-4 cursor-pointer rounded border-zinc-300 text-primary-600 focus:ring-primary-500 transition duration-150 ease-in-out">
                        <label for="remember-me" class="block ml-2 leading-5 text-base">
                            {{ __('Remember Me') }}
                        </label>
                    </div>

                    @if (Route::has('password.request'))
                    <div class="leading-5">
                        <a href="{{ route('password.request') }}" class="text-base font-medium transition duration-150 ease-in-out text-primary-600 hover:text-primary-500 focus:outline-none focus:underline">
                            {{ __('Forgot Your Password?') }}
                        </a>
                    </div>
                    @endif
                </div>

                <input
                    type="submit"
                    class="cursor-pointer inline-flex items-center justify-center block w-full px-4 py-2 mt-6 font-medium leading-5 text-white transition duration-150 ease-in-out border border-transparent rounded-md focus:outline-none focus:ring-indigo bg-primary-600 hover:bg-primary-500 focus:border-primary-700 active:bg-primary-700"
                    value="{{ __('Login') }}">
            </form>
        </div>
    </div>
</div>

@endsection
