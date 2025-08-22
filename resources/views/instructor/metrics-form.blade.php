@extends('layouts.dashboard-layout')

@section('title', 'Metrics Form | Autorank')

@section('content')
<div class="container mx-auto p-4 max-w-lg">
    <h1 class="text-2xl font-bold mb-4">Enter Your Performance Metrics</h1>

    @if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <form action="{{ route('instructor.metrics.store') }}" method="POST">
        @csrf

        {{-- Instruction Score --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="instruction_score">Instruction Score (0-100)</label>
            <input
                type="number"
                name="instruction_score"
                id="instruction_score"
                value="{{ $metrics['Instruction Score'] ?? '' }}"
                required
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                min="0"
                max="100">
        </div>

        {{-- Research Count --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="research_count">Research Documents</label>
            <input
                type="number"
                name="research_count"
                id="research_count"
                value="{{ $metrics['Research Count'] ?? '' }}"
                required
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                min="0">
        </div>

        {{-- Extension Activities --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="extension_activities">Extension Activities</label>
            <input
                type="number"
                name="extension_activities"
                id="extension_activities"
                value="{{ $metrics['Extension Activities'] ?? '' }}"
                required
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                min="0">
        </div>

        {{-- Professional Development --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="professional_dev_hours">Professional Development Hours</label>
            <input
                type="number"
                name="professional_dev_hours"
                id="professional_dev_hours"
                value="{{ $metrics['Professional Development Hours'] ?? '' }}"
                required
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                min="0">
        </div>