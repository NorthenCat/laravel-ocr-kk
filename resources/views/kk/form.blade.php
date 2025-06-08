@extends('layouts.app')

@section('title', isset($kk) ? 'Edit KK' : 'Create KK')

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
                <a href="{{ route('desa.show', isset($kk) ? $kk->getRw->getDesa->id : $rw->getDesa->id) }}"
                    class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">
                    {{ isset($kk) ? $kk->getRw->getDesa->nama_desa : $rw->getDesa->nama_desa }}
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                        clip-rule="evenodd" />
                </svg>
                <a href="{{ route('rw.index', isset($kk) ? [$kk->getRw->getDesa->id, $kk->getRw->id] : [$rw->getDesa->id, $rw->id]) }}"
                    class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">
                    {{ isset($kk) ? $kk->getRw->nama_rw : $rw->nama_rw }}
                </a>
            </div>
        </li>
        @if(isset($kk))
        <li>
            <div class="flex items-center">
                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                        clip-rule="evenodd" />
                </svg>
                <a href="{{ route('kk.index', [$kk->getRw->getDesa->id, $kk->getRw->id, $kk->id]) }}"
                    class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">KK {{ $kk->no_kk }}</a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                        clip-rule="evenodd" />
                </svg>
                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Edit</span>
            </div>
        </li>
        @else
        <li>
            <div class="flex items-center">
                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                        clip-rule="evenodd" />
                </svg>
                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Create KK</span>
            </div>
        </li>
        @endif
    </x-breadcrumb>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">{{ isset($kk) ? 'Edit KK' : 'Create New KK' }}</h3>
                <a href="{{ route('rw.index', isset($kk) ? [$kk->getRw->getDesa->id, $kk->getRw->id] : [$rw->getDesa->id, $rw->id]) }}"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back
                </a>
            </div>

            <form
                action="{{ isset($kk) ? route('kk.update', [$kk->getRw->getDesa->id, $kk->getRw->id, $kk->id]) : route('kk.store', [$rw->getDesa->id, $rw->id]) }}"
                method="POST" class="p-6 space-y-6">
                @csrf
                @if(isset($kk))
                @method('PUT')
                @endif

                @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div>
                    <label for="no_kk" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor KK <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="no_kk" name="no_kk" value="{{ old('no_kk', isset($kk) ? $kk->no_kk : '') }}"
                        placeholder="Enter KK number" required
                        class="block w-full px-3 py-2 border @error('no_kk') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('no_kk')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nama_kepala_keluarga" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Kepala Keluarga <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nama_kepala_keluarga" name="nama_kepala_keluarga"
                        value="{{ old('nama_kepala_keluarga', isset($kk) ? $kk->nama_kepala_keluarga : '') }}"
                        placeholder="Enter head of family name" required
                        class="block w-full px-3 py-2 border @error('nama_kepala_keluarga') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('nama_kepala_keluarga')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('rw.index', isset($kk) ? [$kk->getRw->getDesa->id, $kk->getRw->id] : [$rw->getDesa->id, $rw->id]) }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        {{ isset($kk) ? 'Update' : 'Create' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
