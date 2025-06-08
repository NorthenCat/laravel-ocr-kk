@extends('layouts.app')

@section('title', $rw->nama_rw)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
                </svg>
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
                </svg>
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
                        </svg>
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
                <p class="mt-2 text-gray-600">{{ $rw->getDesa->nama_desa }} - Manage KK and residents for this RW</p>
            </div>
            <div class="flex space-x-3">
                @if(!$rw->getCurrentJobStatus)
                <a href="{{ route('kk.upload', [$rw->getDesa->id, $rw->id]) }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Upload JSON
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
                <h4 class="text-sm font-medium text-blue-800">Processing KK Data</h4>
                <span class="text-xs text-blue-600">Batch ID: {{ $rw->getCurrentJobStatus->batch_id }}</span>
            </div>

            <div class="w-full bg-blue-200 rounded-full h-2 mb-2">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                    style="width: {{ $rw->getCurrentJobStatus->getProgressPercentage() }}%"></div>
            </div>

            <div class="flex justify-between text-xs text-blue-700">
                <span>{{ $rw->getCurrentJobStatus->processed_jobs }} / {{ $rw->getCurrentJobStatus->total_jobs }}
                    completed</span>
                <span>{{ $rw->getCurrentJobStatus->getProgressPercentage() }}%</span>
            </div>

            @if($rw->getCurrentJobStatus->failed_jobs > 0)
            <div class="mt-2 text-xs text-red-600">
                {{ $rw->getCurrentJobStatus->failed_jobs }} jobs failed
            </div>
            @endif

            <div class="mt-3 text-xs text-blue-600">
                Started: {{ $rw->getCurrentJobStatus->started_at->format('M d, Y H:i:s') }}
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
                            </svg>
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
        </div>
    </div>

    <!-- KK List -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Kepala Keluarga (KK) List</h3>
            <p class="text-sm text-gray-600">Manage families in this RW</p>
        </div>

        <div class="p-6">
            @if($rw->getKK->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($rw->getKK as $kk)
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
                            <form action="{{ route('kk.destroy', [$rw->getDesa->id, $rw->id, $kk->id]) }}" method="POST"
                                class="inline">
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
                        <p><span class="font-medium">Anggota:</span> {{ $kk->getAnggota->count() }} orang</p>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('kk.show', [$rw->getDesa->id, $rw->id, $kk->id]) }}"
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            View Details
                            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No KK found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new KK or uploading JSON data.</p>
                <div class="mt-6">
                    @if(!$rw->getCurrentJobStatus)
                    <a href="{{ route('kk.upload', [$rw->getDesa->id, $rw->id]) }}"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 mr-3">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Upload JSON
                    </a>
                    @else
                    <div
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-gray-100 cursor-not-allowed mr-3">
                        <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
</div>
@endsection
