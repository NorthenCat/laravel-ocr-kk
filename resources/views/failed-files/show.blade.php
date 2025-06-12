@extends('layouts.app')

@section('title', 'Failed File Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 ">
    <!-- Breadcrumb -->
    <x-breadcrumb>
        <li class="inline-flex items-center">
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L9 5.414V17a1 1 0 102 0V5.414l5.293 5.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                Dashboard
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                        clip-rule="evenodd" />
                </svg>
                <a href="{{ route('desa.show', $rw->getDesa->id) }}"
                    class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">{{
                    $rw->getDesa->nama_desa }}</a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                        clip-rule="evenodd" />
                </svg>
                <a href="{{ route('rw.index', [$rw->getDesa->id, $rw->id]) }}"
                    class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">{{ $rw->nama_rw }}</a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                        clip-rule="evenodd" />
                </svg>
                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Failed File Details</span>
            </div>
        </li>
    </x-breadcrumb>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-red-50 border-b border-red-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Failed File: {{ $failedFile->filename }}</h3>
                        <p class="text-sm text-gray-600">{{ $failedFile->failure_reason_text }} - {{
                            $failedFile->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <a href="{{ route('rw.index', [$rw->getDesa->id, $rw->id]) }}"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back
                    </a>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <!-- Error Details -->
                @if($failedFile->error_message)
                <div class="bg-red-50 border border-red-200 rounded-md p-4">
                    <h4 class="text-sm font-medium text-red-800 mb-2">Error Message</h4>
                    <p class="text-sm text-red-700">{{ $failedFile->error_message }}</p>
                </div>
                @endif

                <!-- Raw Text -->
                @if($failedFile->raw_text)
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-2">OCR Raw Text</h4>
                    <div class="bg-gray-50 border border-gray-200 rounded-md p-4 max-h-96 overflow-y-auto">
                        <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ $failedFile->raw_text }}</pre>
                    </div>
                </div>
                @endif

                <!-- N8N Response -->
                @if($failedFile->n8n_response)
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-2">N8N Response</h4>
                    <div class="bg-gray-50 border border-gray-200 rounded-md p-4 max-h-96 overflow-y-auto">
                        <pre
                            class="text-sm text-gray-700">{{ json_encode($failedFile->n8n_response, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
                @endif

                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <form
                        action="{{ route('failed-files.mark-processed', [$rw->getDesa->id, $rw->id, $failedFile->id]) }}"
                        method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-green-300 shadow-sm text-sm font-medium rounded-md text-green-700 bg-green-50 hover:bg-green-100"
                            onclick="return confirm('Mark this file as manually processed?')">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Mark as Processed
                        </button>
                    </form>
                    <form action="{{ route('failed-files.destroy', [$rw->getDesa->id, $rw->id, $failedFile->id]) }}"
                        method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100"
                            onclick="return confirm('Are you sure you want to delete this file record?')">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
