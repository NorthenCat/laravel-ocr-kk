@extends('layouts.app')

@section('title', 'Edit Anggota Standalone')

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
                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Edit Anggota</span>
            </div>
        </li>
    </x-breadcrumb>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-yellow-50 border-b border-yellow-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Edit Anggota Tanpa KK</h3>
                        <p class="text-sm text-gray-600">{{ $anggota->nama_lengkap }} - Belum memiliki KK</p>
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

            <form action="{{ route('anggota.standalone.update', [$rw->getDesa->id, $rw->id, $anggota->id]) }}"
                method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

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
                            <h3 class="text-sm font-medium text-red-800">Ada kesalahan pada form</h3>
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

                <!-- KK Assignment Section -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-blue-900 mb-3">Pengaturan KK</h4>
                    <div class="space-y-4">
                        <!-- Assign to existing KK with searchable dropdown -->
                        <div>
                            <label for="assign_to_kk" class="block text-sm font-medium text-gray-700 mb-2">Pindahkan ke
                                KK yang sudah ada</label>

                            <!-- Custom Search Dropdown -->
                            <div class="relative">
                                <div class="search-select-container">
                                    <div class="search-select-input-wrapper">
                                        <input type="text" id="kk_search_input"
                                            placeholder="Ketik nomor KK atau nama kepala keluarga..." autocomplete="off"
                                            oninput="searchKK()" onfocus="showDropdown()" class="search-select-input">
                                        <div class="search-select-arrow" onclick="toggleDropdown()">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>

                                    <div id="kk_dropdown" class="search-select-dropdown hidden">
                                        <div id="kk_results" class="search-select-options">
                                            <!-- Results will be populated here -->
                                        </div>
                                    </div>
                                </div>

                                <!-- Hidden input for form submission -->
                                <input type="hidden" id="assign_to_kk" name="assign_to_kk" value="">
                            </div>
                        </div>

                        <!-- OR create new KK -->
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-blue-50 text-gray-500">ATAU</span>
                            </div>
                        </div>

                        <div>
                            <label for="no_kk" class="block text-sm font-medium text-gray-700 mb-2">Buat KK baru dengan
                                nomor</label>
                            <input type="text" id="no_kk" name="no_kk" value="{{ old('no_kk') }}"
                                placeholder="Masukkan nomor KK baru"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <p class="mt-1 text-xs text-gray-500">Jika diisi, akan membuat KK baru dengan anggota ini
                                sebagai kepala keluarga</p>
                        </div>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap"
                            value="{{ old('nama_lengkap', $anggota->nama_lengkap) }}" required
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="nik" class="block text-sm font-medium text-gray-700 mb-2">NIK</label>
                        <input type="text" id="nik" name="nik" value="{{ old('nik', $anggota->nik) }}"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Kelamin <span class="text-red-500">*</span>
                        </label>
                        <select id="jenis_kelamin" name="jenis_kelamin" required
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="LAKI-LAKI" {{ old('jenis_kelamin', $anggota->jenis_kelamin) === 'LAKI-LAKI' ?
                                'selected' : '' }}>Laki-laki</option>
                            <option value="PEREMPUAN" {{ old('jenis_kelamin', $anggota->jenis_kelamin) === 'PEREMPUAN' ?
                                'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-2">Tempat
                            Lahir</label>
                        <input type="text" id="tempat_lahir" name="tempat_lahir"
                            value="{{ old('tempat_lahir', $anggota->tempat_lahir) }}"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-2">Tanggal
                            Lahir</label>
                        <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                            value="{{ old('tanggal_lahir', $anggota->tanggal_lahir ? $anggota->tanggal_lahir->format('Y-m-d') : '') }}"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <!-- Additional fields similar to regular anggota form -->
                    <div>
                        <label for="agama" class="block text-sm font-medium text-gray-700 mb-2">Agama</label>
                        <input type="text" id="agama" name="agama" value="{{ old('agama', $anggota->agama) }}"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="pendidikan" class="block text-sm font-medium text-gray-700 mb-2">Pendidikan</label>
                        <input type="text" id="pendidikan" name="pendidikan"
                            value="{{ old('pendidikan', $anggota->pendidikan) }}"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="jenis_pekerjaan"
                            class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan</label>
                        <input type="text" id="jenis_pekerjaan" name="jenis_pekerjaan"
                            value="{{ old('jenis_pekerjaan', $anggota->jenis_pekerjaan) }}"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="status_perkawinan" class="block text-sm font-medium text-gray-700 mb-2">Status
                            Perkawinan</label>
                        <input type="text" id="status_perkawinan" name="status_perkawinan"
                            value="{{ old('status_perkawinan', $anggota->status_perkawinan) }}"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('rw.index', [$rw->getDesa->id, $rw->id]) }}"
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
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Custom CSS for search dropdown -->
<style>
    .search-select-container {
        position: relative;
        width: 100%;
    }

    .search-select-input-wrapper {
        position: relative;
    }

    .search-select-input {
        width: 100%;
        padding: 8px 40px 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background-color: white;
        font-size: 14px;
        line-height: 1.5;
        color: #374151;
        transition: all 0.15s ease-in-out;
    }

    .search-select-input:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .search-select-arrow {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        transition: transform 0.2s ease-in-out;
    }

    .search-select-input-wrapper.open .search-select-arrow {
        transform: translateY(-50%) rotate(180deg);
    }

    .search-select-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 50;
        margin-top: 4px;
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        max-height: 200px;
        overflow-y: auto;
    }

    .search-select-options {
        padding: 4px 0;
    }

    .search-select-option {
        padding: 8px 12px;
        cursor: pointer;
        transition: background-color 0.15s ease-in-out;
        border-bottom: 1px solid #f3f4f6;
    }

    .search-select-option:last-child {
        border-bottom: none;
    }

    .search-select-option:hover {
        background-color: #f9fafb;
    }

    .search-select-option.selected {
        background-color: #eef2ff;
        color: #6366f1;
    }

    .search-no-results {
        padding: 12px;
        text-align: center;
        color: #6b7280;
        font-size: 14px;
    }

    .search-loading {
        padding: 12px;
        text-align: center;
        color: #6b7280;
        font-size: 14px;
    }

    .hidden {
        display: none;
    }
</style>

<!-- JavaScript for search dropdown functionality -->
<script>
    let isDropdownOpen = false;
let selectedKKValue = '';
let selectedKKText = '';
let searchTimeout;

// Get KK data from controller
const kkData = @json($kkData);

function showDropdown() {
    const dropdown = document.getElementById('kk_dropdown');
    const wrapper = document.querySelector('.search-select-input-wrapper');

    isDropdownOpen = true;
    dropdown.classList.remove('hidden');
    wrapper.classList.add('open');

    // Show initial results if input is empty
    const searchTerm = document.getElementById('kk_search_input').value.toLowerCase();
    if (searchTerm === '') {
        displayResults(kkData.slice(0, 5)); // Show first 5 KK
    }
}

function hideDropdown() {
    const dropdown = document.getElementById('kk_dropdown');
    const wrapper = document.querySelector('.search-select-input-wrapper');

    isDropdownOpen = false;
    dropdown.classList.add('hidden');
    wrapper.classList.remove('open');
}

function toggleDropdown() {
    if (isDropdownOpen) {
        hideDropdown();
    } else {
        showDropdown();
    }
}

function searchKK() {
    const searchTerm = document.getElementById('kk_search_input').value.toLowerCase().trim();
    const resultsContainer = document.getElementById('kk_results');

    // Clear previous timeout
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }

    // Show loading state
    if (searchTerm.length > 0) {
        resultsContainer.innerHTML = '<div class="search-loading">Mencari...</div>';
        showDropdown();
    }

    // Debounce search
    searchTimeout = setTimeout(() => {
        if (searchTerm === '') {
            // Show first 5 KK when empty
            displayResults(kkData.slice(0, 5));
            return;
        }

        if (searchTerm.length < 2) {
            resultsContainer.innerHTML = '<div class="search-no-results">Ketik minimal 2 karakter untuk mencari</div>';
            return;
        }

        // Filter KK data
        const filteredResults = kkData.filter(kk =>
            kk.search_text.includes(searchTerm)
        );

        displayResults(filteredResults);
    }, 300);
}

function displayResults(results) {
    const resultsContainer = document.getElementById('kk_results');

    if (results.length === 0) {
        resultsContainer.innerHTML = '<div class="search-no-results">Tidak ada KK yang ditemukan</div>';
        return;
    }

    let html = '';
    results.forEach(kk => {
        const noKK = kk.no_kk || '';
        const namaKepala = kk.nama_kepala_keluarga || '';
        const displayText = `${noKK} - ${namaKepala}`;

        html += `
            <div class="search-select-option"
                 data-value="${kk.id}"
                 onclick="selectKK(${kk.id}, '${displayText.replace(/'/g, "\\'")}')">
                <div>
                    <div class="font-medium text-gray-900">${noKK}</div>
                    <div class="text-sm text-gray-500">${namaKepala}</div>
                </div>
            </div>
        `;
    });

    resultsContainer.innerHTML = html;
}

function selectKK(value, text) {
    selectedKKValue = value;
    selectedKKText = text;

    document.getElementById('assign_to_kk').value = value;
    document.getElementById('kk_search_input').value = text;

    // Clear the new KK input when selecting existing KK
    if (value) {
        document.getElementById('no_kk').value = '';
    }

    hideDropdown();
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const container = document.querySelector('.search-select-container');
    if (container && !container.contains(event.target) && isDropdownOpen) {
        hideDropdown();
    }
});

// Handle keyboard navigation
document.getElementById('kk_search_input').addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        hideDropdown();
    } else if (event.key === 'Enter') {
        event.preventDefault();
        const firstOption = document.querySelector('.search-select-option[data-value]');
        if (firstOption) {
            const value = firstOption.getAttribute('data-value');
            const text = firstOption.querySelector('.font-medium').textContent + ' - ' +
                        firstOption.querySelector('.text-sm').textContent;
            selectKK(value, text);
        }
    }
});

// Clear search when typing in new KK input
document.getElementById('no_kk').addEventListener('input', function() {
    if (this.value) {
        document.getElementById('assign_to_kk').value = '';
        document.getElementById('kk_search_input').value = '';
        selectedKKValue = '';
        selectedKKText = '';
    }
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Set initial placeholder
    document.getElementById('kk_search_input').value = '';
});
</script>
@endsection
