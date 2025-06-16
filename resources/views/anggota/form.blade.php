@extends('layouts.app')

@section('title', isset($anggota) ? 'Edit Anggota' : 'Add Anggota')

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
                <a href="{{ route('desa.show', isset($anggota) ? $anggota->getKk->getRw->getDesa->id : $kk->getRw->getDesa->id) }}"
                    class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">
                    {{ isset($anggota) ? $anggota->getKk->getRw->getDesa->nama_desa : $kk->getRw->getDesa->nama_desa }}
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
                <a href="{{ route('rw.index', isset($anggota) ? [$anggota->getKk->getRw->getDesa->id, $anggota->getKk->getRw->id] : [$kk->getRw->getDesa->id, $kk->getRw->id]) }}"
                    class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">
                    {{ isset($anggota) ? $anggota->getKk->getRw->nama_rw : $kk->getRw->nama_rw }}
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
                <a href="{{ route('kk.index', isset($anggota) ? [$anggota->getKk->getRw->getDesa->id, $anggota->getKk->getRw->id, $anggota->getKk->id] : [$kk->getRw->getDesa->id, $kk->getRw->id, $kk->id]) }}"
                    class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">
                    KK {{ isset($anggota) ? $anggota->getKk->no_kk : $kk->no_kk }}
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
                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ isset($anggota) ? 'Edit' : 'Add' }}
                    Anggota</span>
            </div>
        </li>
    </x-breadcrumb>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">{{ isset($anggota) ? 'Edit Anggota Keluarga' : 'Add
                    Anggota Keluarga' }}</h3>
                <a href="{{ route('kk.index', isset($anggota) ? [$anggota->getKk->getRw->getDesa->id, $anggota->getKk->getRw->id, $anggota->getKk->id] : [$kk->getRw->getDesa->id, $kk->getRw->id, $kk->id]) }}"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back
                </a>
            </div>

            <form
                action="{{ isset($anggota) ? route('anggota.update', [$anggota->getKk->getRw->getDesa->id, $anggota->getKk->getRw->id, $anggota->getKk->id, $anggota->id]) : route('anggota.store', [$kk->getRw->getDesa->id, $kk->getRw->id, $kk->id]) }}"
                method="POST" class="p-6 space-y-6">
                @csrf
                @if(isset($anggota))
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

                <!-- Personal Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap"
                            value="{{ old('nama_lengkap', isset($anggota) ? $anggota->nama_lengkap : '') }}" required
                            class="block w-full px-3 py-2 border @error('nama_lengkap') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('nama_lengkap')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nik" class="block text-sm font-medium text-gray-700 mb-2">NIK</label>
                        <input type="text" id="nik" name="nik"
                            value="{{ old('nik', isset($anggota) ? $anggota->nik : '') }}"
                            class="block w-full px-3 py-2 border @error('nik') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('nik')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Kelamin <span class="text-red-500">*</span>
                        </label>
                        <select id="jenis_kelamin" name="jenis_kelamin" required
                            class="block w-full px-3 py-2 border @error('jenis_kelamin') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="LAKI-LAKI" {{ old('jenis_kelamin', isset($anggota) ? $anggota->jenis_kelamin
                                : '') === 'LAKI-LAKI' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="PEREMPUAN" {{ old('jenis_kelamin', isset($anggota) ? $anggota->jenis_kelamin
                                : '') === 'PEREMPUAN' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-2">Tempat
                            Lahir</label>
                        <input type="text" id="tempat_lahir" name="tempat_lahir"
                            value="{{ old('tempat_lahir', isset($anggota) ? $anggota->tempat_lahir : '') }}"
                            class="block w-full px-3 py-2 border @error('tempat_lahir') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('tempat_lahir')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-2">Tanggal
                            Lahir</label>
                        <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                            value="{{ old('tanggal_lahir', isset($anggota) && $anggota->tanggal_lahir ? $anggota->tanggal_lahir->format('Y-m-d') : '') }}"
                            class="block w-full px-3 py-2 border @error('tanggal_lahir') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('tanggal_lahir')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="agama" class="block text-sm font-medium text-gray-700 mb-2">Agama</label>
                        <input type="text" id="agama" name="agama"
                            value="{{ old('agama', isset($anggota) ? $anggota->agama : '') }}"
                            class="block w-full px-3 py-2 border @error('agama') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('agama')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="pendidikan" class="block text-sm font-medium text-gray-700 mb-2">Pendidikan</label>
                        <input type="text" id="pendidikan" name="pendidikan"
                            value="{{ old('pendidikan', isset($anggota) ? $anggota->pendidikan : '') }}"
                            class="block w-full px-3 py-2 border @error('pendidikan') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('pendidikan')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="jenis_pekerjaan"
                            class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan</label>
                        <input type="text" id="jenis_pekerjaan" name="jenis_pekerjaan"
                            value="{{ old('jenis_pekerjaan', isset($anggota) ? $anggota->jenis_pekerjaan : '') }}"
                            class="block w-full px-3 py-2 border @error('jenis_pekerjaan') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('jenis_pekerjaan')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status_perkawinan" class="block text-sm font-medium text-gray-700 mb-2">Status
                            Perkawinan</label>
                        <input type="text" id="status_perkawinan" name="status_perkawinan"
                            value="{{ old('status_perkawinan', isset($anggota) ? $anggota->status_perkawinan : '') }}"
                            class="block w-full px-3 py-2 border @error('status_perkawinan') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('status_perkawinan')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status_hubungan_dalam_keluarga"
                            class="block text-sm font-medium text-gray-700 mb-2">Hubungan dalam Keluarga</label>
                        <input type="text" id="status_hubungan_dalam_keluarga" name="status_hubungan_dalam_keluarga"
                            value="{{ old('status_hubungan_dalam_keluarga', isset($anggota) ? $anggota->status_hubungan_dalam_keluarga : '') }}"
                            class="block w-full px-3 py-2 border @error('status_hubungan_dalam_keluarga') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('status_hubungan_dalam_keluarga')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="golongan_darah" class="block text-sm font-medium text-gray-700 mb-2">Golongan
                            Darah</label>
                        <input type="text" id="golongan_darah" name="golongan_darah"
                            value="{{ old('golongan_darah', isset($anggota) ? $anggota->golongan_darah : '') }}"
                            class="block w-full px-3 py-2 border @error('golongan_darah') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('golongan_darah')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="kewarganegaraan"
                            class="block text-sm font-medium text-gray-700 mb-2">Kewarganegaraan</label>
                        <input type="text" id="kewarganegaraan" name="kewarganegaraan"
                            value="{{ old('kewarganegaraan', isset($anggota) ? $anggota->kewarganegaraan : '') }}"
                            class="block w-full px-3 py-2 border @error('kewarganegaraan') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('kewarganegaraan')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="ayah" class="block text-sm font-medium text-gray-700 mb-2">Nama Ayah</label>
                        <input type="text" id="ayah" name="ayah"
                            value="{{ old('ayah', isset($anggota) ? $anggota->ayah : '') }}"
                            class="block w-full px-3 py-2 border @error('ayah') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('ayah')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="ibu" class="block text-sm font-medium text-gray-700 mb-2">Nama Ibu</label>
                        <input type="text" id="ibu" name="ibu"
                            value="{{ old('ibu', isset($anggota) ? $anggota->ibu : '') }}"
                            class="block w-full px-3 py-2 border @error('ibu') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('ibu')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="no_paspor" class="block text-sm font-medium text-gray-700 mb-2">No. Paspor</label>
                        <input type="text" id="no_paspor" name="no_paspor"
                            value="{{ old('no_paspor', isset($anggota) ? $anggota->no_paspor : '') }}"
                            class="block w-full px-3 py-2 border @error('no_paspor') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('no_paspor')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="no_kitap" class="block text-sm font-medium text-gray-700 mb-2">No. KITAP</label>
                        <input type="text" id="no_kitap" name="no_kitap"
                            value="{{ old('no_kitap', isset($anggota) ? $anggota->no_kitap : '') }}"
                            class="block w-full px-3 py-2 border @error('no_kitap') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('no_kitap')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Additional Information Section -->
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Informasi Alamat</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                            <textarea id="alamat" name="alamat" rows="2"
                                class="block w-full px-3 py-2 border @error('alamat') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('alamat', isset($anggota) ? $anggota->alamat : '') }}</textarea>
                            @error('alamat')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="rt" class="block text-sm font-medium text-gray-700 mb-2">RT</label>
                            <input type="text" id="rt" name="rt"
                                value="{{ old('rt', isset($anggota) ? $anggota->rt : '') }}"
                                class="block w-full px-3 py-2 border @error('rt') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('rt')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="rw" class="block text-sm font-medium text-gray-700 mb-2">RW</label>
                            <input type="text" id="rw" name="rw"
                                value="{{ old('rw', isset($anggota) ? $anggota->rw : '') }}"
                                class="block w-full px-3 py-2 border @error('rw') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('rw')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="kode_pos" class="block text-sm font-medium text-gray-700 mb-2">Kode Pos</label>
                            <input type="text" id="kode_pos" name="kode_pos"
                                value="{{ old('kode_pos', isset($anggota) ? $anggota->kode_pos : '') }}"
                                class="block w-full px-3 py-2 border @error('kode_pos') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('kode_pos')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="desa_kelurahan"
                                class="block text-sm font-medium text-gray-700 mb-2">Desa/Kelurahan</label>
                            <input type="text" id="desa_kelurahan" name="desa_kelurahan"
                                value="{{ old('desa_kelurahan', isset($anggota) ? $anggota->desa_kelurahan : '') }}"
                                class="block w-full px-3 py-2 border @error('desa_kelurahan') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('desa_kelurahan')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="kecamatan"
                                class="block text-sm font-medium text-gray-700 mb-2">Kecamatan</label>
                            <input type="text" id="kecamatan" name="kecamatan"
                                value="{{ old('kecamatan', isset($anggota) ? $anggota->kecamatan : '') }}"
                                class="block w-full px-3 py-2 border @error('kecamatan') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('kecamatan')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="kabupaten_kota"
                                class="block text-sm font-medium text-gray-700 mb-2">Kabupaten/Kota</label>
                            <input type="text" id="kabupaten_kota" name="kabupaten_kota"
                                value="{{ old('kabupaten_kota', isset($anggota) ? $anggota->kabupaten_kota : '') }}"
                                class="block w-full px-3 py-2 border @error('kabupaten_kota') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('kabupaten_kota')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="provinsi" class="block text-sm font-medium text-gray-700 mb-2">Provinsi</label>
                            <input type="text" id="provinsi" name="provinsi"
                                value="{{ old('provinsi', isset($anggota) ? $anggota->provinsi : '') }}"
                                class="block w-full px-3 py-2 border @error('provinsi') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('provinsi')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="kk_disahkan_tanggal" class="block text-sm font-medium text-gray-700 mb-2">KK
                                Disahkan Tanggal</label>
                            <input type="date" id="kk_disahkan_tanggal" name="kk_disahkan_tanggal"
                                value="{{ old('kk_disahkan_tanggal', isset($anggota) && $anggota->kk_disahkan_tanggal ? $anggota->kk_disahkan_tanggal->format('Y-m-d') : '') }}"
                                class="block w-full px-3 py-2 border @error('kk_disahkan_tanggal') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('kk_disahkan_tanggal')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('kk.index', isset($anggota) ? [$anggota->getKk->getRw->getDesa->id, $anggota->getKk->getRw->id, $anggota->getKk->id] : [$kk->getRw->getDesa->id, $kk->getRw->id, $kk->id]) }}"
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
                        {{ isset($anggota) ? 'Update' : 'Save' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
