@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Breadcrumb -->
    <x-breadcrumb>
        <li class="inline-flex items-center">
            <span class="inline-flex items-center text-sm font-medium text-gray-500">
                Dashboard
            </span>
        </li>
    </x-breadcrumb>

    <div class="mb-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">Dashboard - Data Desa</h1>
            <a href="{{ route('desa.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Tambah Desa Baru
            </a>
        </div>
    </div>

    @if (session('status'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
        {{ session('status') }}
    </div>
    @endif

    @if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Daftar Desa</h2>
            <p class="text-sm text-gray-600 mt-1">Kelola data desa dan RW yang dapat Anda akses</p>
        </div>

        <div class="p-6">
            @if(count($desas) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($desas as $desa)
                <div
                    class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $desa['nama_desa'] ?? 'Unknown Village'
                                }}</h3>
                        </div>

                        <div class="flex justify-between items-center text-sm text-gray-500 mb-4">
                            <span>
                                <i class="fas fa-building mr-1"></i>
                                {{ $desa['get_rw_count'] ?? 0 }} RW
                            </span>
                            <span>
                                <i class="fas fa-users mr-1"></i>
                                {{ $desa['get_k_k_count'] ?? 0 }} KK
                            </span>
                        </div>

                        <div class="flex space-x-2">
                            <a href="{{ route('desa.show', $desa['id']) }}"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-md text-sm font-medium">
                                Lihat Detail
                            </a>
                            <a href="{{ route('desa.edit', $desa['id']) }}"
                                class="bg-gray-100 hover:bg-gray-200 text-gray-800 py-2 px-4 rounded-md text-sm font-medium">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-home text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Desa</h3>
                <p class="text-gray-500 mb-6">Mulai dengan membuat desa pertama Anda atau minta akses ke desa yang sudah
                    ada.</p>
                <a href="{{ route('desa.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Buat Desa Pertama
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
