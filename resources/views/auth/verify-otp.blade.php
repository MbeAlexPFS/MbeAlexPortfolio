@extends('layouts.app')

@section('title', 'Vérification OTP')

@section('content')
    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-dark-text text-center">Vérification OTP</h1>
        <p class="mt-2 text-sm text-gray-500 dark:text-dark-muted text-center">Un code à 6 chiffres vous a été envoyé par email.</p>

        <form method="POST" action="{{ route('auth.verify-otp') }}" class="mt-8 space-y-5" x-on:submit="$el.querySelector('button[type=submit]').disabled = true">
            @csrf
            <div>
                <label for="otp" class="block text-sm font-medium text-gray-700 dark:text-dark-muted">Code OTP</label>
                <input type="text" name="otp" id="otp" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required autofocus
                    class="mt-1 block w-full text-center text-2xl tracking-[0.5em] rounded-lg border border-gray-300 dark:border-dark-border bg-white dark:bg-dark-bg text-gray-900 dark:text-dark-text px-3 py-3 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                @error('otp') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="w-full bg-indigo-600 dark:bg-indigo-500 text-white py-2.5 rounded-lg font-medium hover:bg-indigo-700 dark:hover:bg-indigo-600 transition text-sm">
                Vérifier
            </button>
        </form>
    </div>
@endsection
