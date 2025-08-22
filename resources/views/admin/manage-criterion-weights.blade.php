@extends('layouts.dashboard-layout')

@section('title', 'Manage Criterions | Autorank')

@section('content')
<div class="container mx-auto p-4 max-w-lg">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Set AHP Weights</h1>

    {{-- Success Message --}}
    @if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="font-bold">Error!</span>
        <span class="block sm:inline">Please correct the following issues:</span>
        <ul class="mt-2 list-disc list-inside">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('ahp.weights.update') }}" method="POST" class="bg-white shadow-md rounded-lg px-8 pt-6 pb-8 mb-4">
        @csrf

        <p class="text-sm text-gray-600 mb-6">
            Enter the weight for each criterion. The total sum of all weights must be exactly 1.0.
        </p>

        @foreach($criteria as $criterion)
        <div class="mb-4">
            <label for="weight_{{ $criterion->id }}" class="block text-gray-700 text-sm font-bold mb-2">
                {{ $criterion->name }}
            </label>
            <input
                type="number"
                step="0.01"
                min="0"
                max="1"
                name="weights[{{ $criterion->id }}]"
                value="{{ $weights[$criterion->id] ?? '0.00' }}" {{-- Pre-fills the input with existing data --}}
                required
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        @endforeach

        <div class="flex items-center justify-end mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150">
                Update Weights
            </button>
        </div>
    </form>
</div>
@endsection