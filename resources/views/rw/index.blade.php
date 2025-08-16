@extends('layouts.app')

@section('title', $rw->nama_rw)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $rw->nama_rw }}</span>
            </div>
        </li>
    </x-breadcrumb>

    <!-- Session Messages -->
    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button type="button"
                        class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100"
                        onclick="this.parentElement.parentElement.parentElement.parentElement.style.display='none'">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button type="button" class="inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100"
                        onclick="this.parentElement.parentElement.parentElement.parentElement.style.display='none'">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Header with Job Status -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $rw->nama_rw }}</h1>
                <p class="mt-2 text-gray-600">{{ $rw->getDesa->nama_desa }} - Manage KK and residents for this RW
                </p>
            </div>
            <div class="flex space-x-3">
                @if(!$rw->getCurrentJobStatus)
                <a href="{{ route('kk.upload', [$rw->getDesa->id, $rw->id]) }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Upload Images
                </a>
                @else
                <div
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-gray-100 cursor-not-allowed">
                    <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Processing...
                </div>
                @endif
                <!-- Export Excel Dropdown -->
                <div class="relative inline-block text-left">
                    <div>
                        <button type="button"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                            id="export-menu-button" aria-expanded="true" aria-haspopup="true">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export Excel
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>

                    <div class="origin-top-right absolute right-0 mt-2 w-64 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden"
                        role="menu" aria-orientation="vertical" aria-labelledby="export-menu-button" tabindex="-1"
                        id="export-dropdown">
                        <div class="py-1" role="none">
                            <a href="{{ route('rw.export.excel', [$rw->getDesa->id, $rw->id]) }}"
                                class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                role="menuitem" tabindex="-1">
                                <svg class="w-4 h-4 mr-3 text-emerald-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <div>
                                    <div class="font-medium">With Filename</div>
                                    <div class="text-xs text-gray-500">Include image filename column</div>
                                </div>
                            </a>
                            <a href="{{ route('rw.export.excel.no-filename', [$rw->getDesa->id, $rw->id]) }}"
                                class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                role="menuitem" tabindex="-1">
                                <svg class="w-4 h-4 mr-3 text-emerald-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <div>
                                    <div class="font-medium">Without Filename</div>
                                    <div class="text-xs text-gray-500">Exclude image filename column</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <a href="{{ route('kk.create', [$rw->getDesa->id, $rw->id]) }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add KK
                </a>
                <a href="{{ route('rw.edit', [$rw->getDesa->id, $rw->id]) }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit RW
                </a>
            </div>
        </div>

        @if($rw->getCurrentJobStatus)
        <!-- Active Job Status Card -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4" id="job-status-card">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-medium text-blue-800">
                    @if($rw->getCurrentJobStatus->status === 'completed')
                    KK Data Processing Complete
                    @else
                    Processing KK Data
                    @endif
                </h4>
                <span class="text-xs text-blue-600">Batch ID: {{ $rw->getCurrentJobStatus->batch_id }}</span>
            </div>

            <div class="w-full bg-blue-200 rounded-full h-2 mb-2">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                    style="width: {{ $rw->getCurrentJobStatus->getProgressPercentage() }}%"></div>
            </div>

            <div class="flex justify-between text-xs text-blue-700">
                <span>{{ $rw->getCurrentJobStatus->processed_jobs }} / {{ $rw->getCurrentJobStatus->total_jobs }}
                    @if($rw->getCurrentJobStatus->status === 'completed')
                    completed
                    @else
                    processed
                    @endif
                </span>
                <span>{{ $rw->getCurrentJobStatus->getProgressPercentage() }}%</span>
            </div>

            @if($rw->getCurrentJobStatus->failed_jobs > 0)
            <div class="mt-2 text-xs text-red-600">
                {{ $rw->getCurrentJobStatus->failed_jobs }} jobs failed
            </div>
            @endif

            <div class="mt-3 text-xs text-blue-600">
                @if($rw->getCurrentJobStatus->status === 'completed')
                Completed: {{ $rw->getCurrentJobStatus->completed_at->format('M d, Y H:i:s') }}
                @else
                Started: {{ $rw->getCurrentJobStatus->started_at->format('M d, Y H:i:s') }}
                <span class="ml-2 inline-flex items-center">
                    <svg class="w-3 h-3 mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Processing...
                </span>
                @endif
            </div>
        </div>

        <!-- Auto-refresh script for job status -->
        <script>
            @if($rw->getCurrentJobStatus)
            setInterval(function() {
                fetch(window.location.href, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newJobCard = doc.querySelector('#job-status-card');
                    const currentJobCard = document.querySelector('#job-status-card');

                    if (newJobCard && currentJobCard) {
                        currentJobCard.outerHTML = newJobCard.outerHTML;
                    } else if (!newJobCard && currentJobCard) {
                        // Job completed, reload page
                        window.location.reload();
                    }
                })
                .catch(console.error);
            }, 3000); // Refresh every 3 seconds
            @endif
        </script>
        @endif

        <script>
            // Export dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            const exportButton = document.getElementById('export-menu-button');
            const exportDropdown = document.getElementById('export-dropdown');

            if (exportButton && exportDropdown) {
                exportButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    exportDropdown.classList.toggle('hidden');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!exportButton.contains(e.target) && !exportDropdown.contains(e.target)) {
                        exportDropdown.classList.add('hidden');
                    }
                });
            }
        });
        </script>

        @if($rw->getJobStatus->count() > 0 && !$rw->getCurrentJobStatus)
        <!-- Recent Job History -->
        <div class="mt-6">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Recent Processing History</h4>
            <div class="space-y-2">
                @foreach($rw->getJobStatus->take(3) as $jobStatus)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        @if($jobStatus->isCompleted())
                        <div class="flex-shrink-0 w-2 h-2 bg-green-400 rounded-full"></div>
                        <span class="text-sm text-gray-900">Completed</span>
                        @elseif($jobStatus->hasFailed())
                        <div class="flex-shrink-0 w-2 h-2 bg-red-400 rounded-full"></div>
                        <span class="text-sm text-gray-900">Failed</span>
                        @endif
                        <span class="text-xs text-gray-500">{{ $jobStatus->processed_jobs }}/{{ $jobStatus->total_jobs
                            }} items</span>
                        @if($jobStatus->failed_jobs > 0)
                        <span class="text-xs text-red-500">({{ $jobStatus->failed_jobs }} failed)</span>
                        @endif
                    </div>
                    <span class="text-xs text-gray-500">{{ $jobStatus->completed_at ?
                        $jobStatus->completed_at->diffForHumans() : $jobStatus->started_at->diffForHumans() }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Statistics -->
        <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total KK</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $rw->get_k_k_count }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Warga</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $rw->get_warga_count }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total RT</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $totalRT }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabbed Container -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6 overflow-x-auto" aria-label="Tabs">
                <button onclick="switchTab('kk-list')" id="tab-kk-list"
                    class="tab-button py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap border-indigo-500 text-indigo-600">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        KK List
                        <span class="ml-2 bg-indigo-100 text-indigo-600 py-0.5 px-2.5 rounded-full text-xs font-medium">
                            {{ $rw->getKK->where('no_kk', '!=', '0000000000000000')->count() }}
                        </span>
                    </div>
                </button>

                @if(isset($standaloneAnggota) && $standaloneAnggota->count() > 0)
                <button onclick="switchTab('standalone-anggota')" id="tab-standalone-anggota"
                    class="tab-button py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        Anggota Tanpa KK
                        <span class="ml-2 bg-yellow-100 text-yellow-600 py-0.5 px-2.5 rounded-full text-xs font-medium">
                            {{ $standaloneAnggota->count() }}
                        </span>
                    </div>
                </button>
                @endif

                @if(isset($anggotaTanpaNik) && $anggotaTanpaNik->count() > 0)
                <button onclick="switchTab('tanpa-nik')" id="tab-tanpa-nik"
                    class="tab-button py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9zM13.73 21a2 2 0 01-3.46 0"
                                clip-rule="evenodd" />
                        </svg>
                        Anggota Tanpa NIK
                        <span class="ml-2 bg-orange-100 text-orange-600 py-0.5 px-2.5 rounded-full text-xs font-medium">
                            {{ $anggotaTanpaNik->count() }}
                        </span>
                    </div>
                </button>
                @endif

                @if($failedFiles->count() > 0)
                <button onclick="switchTab('failed-files')" id="tab-failed-files"
                    class="tab-button py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Failed Files
                        <span class="ml-2 bg-red-100 text-red-600 py-0.5 px-2.5 rounded-full text-xs font-medium">
                            {{ $failedFiles->count() }}
                        </span>
                    </div>
                </button>
                @endif
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- KK List Tab -->
            <div id="content-kk-list" class="tab-pane active">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Kepala Keluarga (KK) List</h3>
                    <p class="text-sm text-gray-600">Manage families in this RW</p>
                </div>

                <div class="p-6">
                    @if($rw->getKK->where('no_kk', '!=', '0000000000000000')->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($rw->getKK as $kk)
                        @if($kk->no_kk !== '0000000000000000')
                        <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-semibold text-gray-900">{{ $kk->nama_kepala_keluarga }}</h4>
                                <div class="flex space-x-2">
                                    <a href="{{ route('kk.edit', [$rw->getDesa->id, $rw->id, $kk->id]) }}"
                                        class="text-indigo-600 hover:text-indigo-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('kk.destroy', [$rw->getDesa->id, $rw->id, $kk->id]) }}"
                                        method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800"
                                            onclick="return confirm('Are you sure?')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="space-y-2 text-sm text-gray-600">

                                <p><span class="font-medium">No. KK:</span> {{ $kk->no_kk }}</p>
                                <p><span class="font-medium">Filename:</span> {{ $kk->getWarga[0]->img_name ?? 'INPUT
                                    MANUAL' }}</p>
                                <p><span class="font-medium">Anggota:</span> {{ $kk->getWarga->count() }} orang</p>
                            </div>

                            <div class="mt-4">
                                <a href="{{ route('kk.index', [$rw->getDesa->id, $rw->id, $kk->id]) }}"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    View Details
                                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No KK found</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new KK or uploading JSON data.
                        </p>
                        <div class="mt-6">
                            @if(!$rw->getCurrentJobStatus)
                            <a href="{{ route('kk.upload', [$rw->getDesa->id, $rw->id]) }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 mr-3">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                Upload Images
                            </a>
                            @else
                            <div
                                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-gray-100 cursor-not-allowed mr-3">
                                <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Processing...
                            </div>
                            @endif
                            <a href="{{ route('kk.create', [$rw->getDesa->id, $rw->id]) }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Add KK
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Standalone Anggota Tab -->
            @if(isset($standaloneAnggota) && $standaloneAnggota->count() > 0)
            <div id="content-standalone-anggota" class="tab-pane hidden">
                <div class="px-6 py-4 bg-yellow-50 border-b border-yellow-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Anggota Tanpa KK</h3>
                            <p class="text-sm text-gray-600">{{ $standaloneAnggota->count() }} anggota belum memiliki KK
                                yang valid
                            </p>
                        </div>
                        <div
                            class="flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-full">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            Perlu Perhatian
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($standaloneAnggota as $anggota)
                            <div
                                class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-semibold text-gray-900">{{ $anggota->nama_lengkap ??
                                            'Nama tidak
                                            tersedia'
                                            }}</h4>
                                        @if($anggota->nik)
                                        <p class="text-xs text-gray-600 mt-1">NIK: {{ $anggota->nik }}</p>
                                        @endif
                                        @if($anggota->img_name)
                                        <p class="text-xs text-gray-600 mt-1">Filename: {{ $anggota->img_name }}</p>
                                        @endif
                                        @if($anggota->getKk && $anggota->getKk->no_kk === '0000000000000000')
                                        <p class="text-xs text-orange-600 mt-1">KK Sementara: {{ $anggota->getKk->no_kk
                                            }}</p>
                                        @else
                                        <p class="text-xs text-red-600 mt-1">Belum ada KK</p>
                                        @endif
                                    </div>
                                    <div class="flex space-x-1 ml-2">
                                        <a href="{{ route('anggota.standalone.edit', [$rw->getDesa->id, $rw->id, $anggota->id]) }}"
                                            class="text-indigo-600 hover:text-indigo-800" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form
                                            action="{{ route('anggota.standalone.destroy', [$rw->getDesa->id, $rw->id, $anggota->id]) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800"
                                                onclick="return confirm('Yakin ingin menghapus anggota ini?')"
                                                title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="space-y-1 text-xs text-gray-600 mb-3">
                                    @if($anggota->jenis_kelamin)
                                    <p><span class="font-medium">Kelamin:</span> {{ $anggota->jenis_kelamin }}</p>
                                    @endif
                                    @if($anggota->tanggal_lahir)
                                    <p><span class="font-medium">Lahir:</span> {{
                                        \Carbon\Carbon::parse($anggota->tanggal_lahir)->format('d/m/Y') }}</p>
                                    @endif
                                    @if($anggota->tempat_lahir)
                                    <p><span class="font-medium">Tempat:</span> {{ $anggota->tempat_lahir }}</p>
                                    @endif
                                    @if($anggota->rw)
                                    <p><span class="font-medium">RW:</span> {{ $anggota->rw }}</p>
                                    @endif
                                </div>

                                <div class="flex justify-between items-center pt-3 border-t border-yellow-200">
                                    <span class="text-xs text-red-700 font-medium">KK Sementara</span>
                                    <a href="{{ route('anggota.standalone.edit', [$rw->getDesa->id, $rw->id, $anggota->id]) }}"
                                        class="inline-flex items-center px-2 py-1 bg-indigo-100 text-indigo-700 text-xs font-medium rounded hover:bg-indigo-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Atur KK
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-4 p-3 bg-yellow-100 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-yellow-600 mr-2 mt-0.5" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zm-4 4a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                            </div>
                            <div class="text-sm text-yellow-800">
                                <p class="font-medium">Anggota-anggota ini memiliki KK sementara</p>
                                <p class="mt-1">Mereka memiliki KK dengan nomor 16 angka nol yang perlu diperbaiki.
                                    Klik "Atur KK" untuk menambahkan ke KK yang sudah ada atau membuat KK baru
                                    dengan nomor yang benar.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <!-- Anggota Tanpa NIK Tab -->
            @if(isset($anggotaTanpaNik) && $anggotaTanpaNik->count() > 0)
            <div id="content-tanpa-nik" class="tab-pane hidden">
                <div class="px-6 py-4 bg-orange-50 border-b border-orange-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Anggota Tanpa NIK</h3>
                            <p class="text-sm text-gray-600">{{ $anggotaTanpaNik->count() }} anggota belum memiliki
                                NIK yang valid</p>
                        </div>
                        <div
                            class="flex items-center px-3 py-1 bg-orange-100 text-orange-800 text-sm font-medium rounded-full">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9zM13.73 21a2 2 0 01-3.46 0"
                                    clip-rule="evenodd" />
                            </svg>
                            Data Tidak Lengkap
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($anggotaTanpaNik as $anggota)
                            <div
                                class="bg-orange-50 border border-orange-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-semibold text-gray-900">{{ $anggota->nama_lengkap ??
                                            'Nama tidak tersedia' }}</h4>

                                        <div class="mt-2 text-xs text-red-600 font-medium">
                                            <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            NIK: {{ $anggota->nik ?: 'Belum ada' }}
                                        </div>
                                    </div>
                                    <div class="flex space-x-1 ml-2">
                                        <a href="{{ route('anggota.edit', [$rw->getDesa->id, $rw->id, $anggota->getKk->id, $anggota->id]) }}"
                                            class="text-orange-600 hover:text-orange-800" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form
                                            action="{{ route('anggota.destroy', [$rw->getDesa->id, $rw->id, $anggota->getKk->id, $anggota->id]) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800"
                                                onclick="return confirm('Yakin ingin menghapus anggota ini?')"
                                                title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="space-y-1 text-xs text-gray-600 mb-3">
                                    @if($anggota->jenis_kelamin)
                                    <p><span class="font-medium">Kelamin:</span> {{ $anggota->jenis_kelamin }}</p>
                                    @endif
                                    @if($anggota->tanggal_lahir)
                                    <p><span class="font-medium">Lahir:</span> {{
                                        \Carbon\Carbon::parse($anggota->tanggal_lahir)->format('d/m/Y') }}</p>
                                    @endif
                                    @if($anggota->tempat_lahir)
                                    <p><span class="font-medium">Tempat:</span> {{ $anggota->tempat_lahir }}</p>
                                    @endif
                                </div>

                                <div class="flex justify-between items-center pt-3 border-t border-orange-200">
                                    <span class="text-xs text-red-700 font-medium">NIK Kosong</span>
                                    <a href="{{ route('anggota.edit', [$rw->getDesa->id, $rw->id, $anggota->getKk->id, $anggota->id]) }}"
                                        class="inline-flex items-center px-2 py-1 bg-orange-100 text-orange-700 text-xs font-medium rounded hover:bg-orange-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Lengkapi NIK
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-orange-100 rounded-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-orange-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zm-4 4a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                        </div>
                        <div class="text-sm text-orange-800">
                            <p class="font-medium">Anggota-anggota ini belum memiliki NIK yang valid</p>
                            <p class="mt-1">NIK diperlukan untuk identifikasi resmi. Klik "Lengkapi NIK"
                                untuk
                                menambahkan atau memperbaiki data NIK.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Failed Files Tab -->
        @if($failedFiles->count() > 0)
        <div id="content-failed-files" class="tab-pane hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Files Requiring Manual Processing</h3>
                    <p class="text-sm text-gray-600">Files that failed processing or contain non-KK data
                    </p>
                </div>
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    {{ $failedFiles->count() }} files
                </span>
            </div>
            <div class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Filename
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Reason
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($failedFiles as $file)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $file->original_filename }}
                                    </div>
                                    @if($file->error_message)
                                    <div class="text-sm text-gray-500 truncate max-w-xs">{{
                                        $file->error_message }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($file->failure_reason === 'not_kk') bg-yellow-100 text-yellow-800
                                            @elseif($file->failure_reason === 'processing_error') bg-red-100 text-red-800
                                            @else bg-orange-100 text-orange-800 @endif">
                                        {{ $file->failure_reason_text }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $file->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('failed-files.show', [$rw->getDesa->id, $rw->id, $file->id]) }}"
                                        class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        View
                                    </a>
                                    <form
                                        action="{{ route('failed-files.mark-processed', [$rw->getDesa->id, $rw->id, $file->id]) }}"
                                        method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="inline-flex items-center px-2.5 py-1.5 border border-green-300 shadow-sm text-xs font-medium rounded text-green-700 bg-green-50 hover:bg-green-100"
                                            onclick="return confirm('Mark this file as manually processed?')">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            Mark Done
                                        </button>
                                    </form>
                                    <form
                                        action="{{ route('failed-files.destroy', [$rw->getDesa->id, $rw->id, $file->id]) }}"
                                        method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center px-2.5 py-1.5 border border-red-300 shadow-sm text-xs font-medium rounded text-red-700 bg-red-50 hover:bg-red-100"
                                            onclick="return confirm('Are you sure you want to delete this file record?')">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif


        <!-- Tab Switching JavaScript -->
        <script>
            function switchTab(tabName) {
        // Hide all tab panes
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.add('hidden');
            pane.classList.remove('active');
        });

        // Remove active state from all tab buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('border-indigo-500', 'text-indigo-600');
            button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        });

        // Show selected tab pane
        const selectedPane = document.getElementById('content-' + tabName);
        if (selectedPane) {
            selectedPane.classList.remove('hidden');
            selectedPane.classList.add('active');
        }

        // Activate selected tab button
        const selectedButton = document.getElementById('tab-' + tabName);
        if (selectedButton) {
            selectedButton.classList.add('border-indigo-500', 'text-indigo-600');
            selectedButton.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        }
    }

    // Initialize tabs on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Check if any tab is currently active, if not, activate the first available tab
        const activeTabs = document.querySelectorAll('.tab-pane.active');
        if (activeTabs.length === 0) {
            // Find the first available tab and activate it
            const firstTab = document.querySelector('.tab-button');
            if (firstTab) {
                const tabName = firstTab.id.replace('tab-', '');
                switchTab(tabName);
            }
        }

        // Add click event listeners to all tab buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function() {
                const tabName = this.id.replace('tab-', '');
                switchTab(tabName);
            });
        });
    });

    // Auto-refresh script for job status and failed files
    @if($rw->getCurrentJobStatus)
    setInterval(function() {
        fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Update job status card
            const newJobCard = doc.querySelector('#job-status-card');
            const currentJobCard = document.querySelector('#job-status-card');

            if (newJobCard && currentJobCard) {
                currentJobCard.outerHTML = newJobCard.outerHTML;
            } else if (!newJobCard && currentJobCard) {
                // Job completed, reload page
                window.location.reload();
            }

            // Update tab navigation and content if new failed files appear
            const newTabNavigation = doc.querySelector('.border-b.border-gray-200 nav');
            const currentTabNavigation = document.querySelector('.border-b.border-gray-200 nav');

            if (newTabNavigation && currentTabNavigation) {
                // Check if failed files tab appeared
                const newFailedTab = newTabNavigation.querySelector('#tab-failed-files');
                const currentFailedTab = currentTabNavigation.querySelector('#tab-failed-files');

                if (newFailedTab && !currentFailedTab) {
                    // New failed files appeared, reload page
                    window.location.reload();
                }
            }
        })
        .catch(console.error);
    }, 3000); // Refresh every 3 seconds
    @endif
        </script>

        <!-- Custom CSS for Tabs -->
        <style>
            .tab-pane {
                min-height: 400px;
            }

            .tab-button {
                transition: all 0.2s ease-in-out;
                cursor: pointer;
            }

            .tab-button:hover {
                opacity: 0.8;
            }

            .tab-content {
                background: white;
            }

            /* Ensure proper display of active tab */
            .tab-pane.active {
                display: block !important;
            }

            .tab-pane.hidden {
                display: none !important;
            }
        </style>
        </>
    </div>
</div>
@endsection