@extends('layouts.app')

@section('title', 'Upload KK Data')

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
                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Upload KK Data</span>
            </div>
        </li>
    </x-breadcrumb>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Upload KK Images</h3>
                <p class="text-sm text-gray-600">Upload multiple image files containing KK data</p>
            </div>

            <form action="{{ route('kk.upload.process', [$rw->getDesa->id, $rw->id]) }}" method="POST"
                enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf

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
                    <div class="flex items-center justify-between mb-2">
                        <label for="kk_images" class="block text-sm font-medium text-gray-700">
                            KK Images <span class="text-red-500">*</span>
                        </label>
                        <span id="file-counter" class="text-sm text-gray-500 hidden">
                            <span id="file-count">0</span> file(s) selected
                        </span>
                    </div>

                    <!-- Drop Zone -->
                    <div id="drop-zone"
                        class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md transition-colors duration-200 hover:border-indigo-400 hover:bg-gray-50">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                viewBox="0 0 48 48">
                                <path
                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="kk_images"
                                    class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                    <span>Upload files</span>
                                    <input id="kk_images" name="kk_images[]" type="file" accept="image/*" multiple
                                        class="sr-only" required>
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, JPEG files up to 10MB each</p>
                        </div>
                    </div>

                    <!-- File Preview Area -->
                    <div id="file-preview" class="mt-4 hidden">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Selected Files:</h4>
                        <div id="file-list" class="space-y-2 max-h-60 overflow-y-auto">
                            <!-- File items will be inserted here -->
                        </div>
                        <button type="button" id="clear-files"
                            class="mt-3 text-sm text-red-600 hover:text-red-800 font-medium">
                            Clear all files
                        </button>
                    </div>

                    @error('kk_images')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('kk_images.*')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-blue-800">Upload Instructions</h4>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>You can upload multiple KK image files at once:</p>
                                <ul class="list-disc list-inside mt-1">
                                    <li>Supported formats: PNG, JPG, JPEG</li>
                                    <li>Maximum file size: 10MB per image</li>
                                    <li>Images will be processed using OCR to extract KK data</li>
                                    <li>Processing will be done in the background</li>
                                </ul>
                            </div>
                        </div>
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
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Upload & Process
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('kk_images');
    const filePreview = document.getElementById('file-preview');
    const fileList = document.getElementById('file-list');
    const fileCounter = document.getElementById('file-counter');
    const fileCount = document.getElementById('file-count');
    const clearFilesBtn = document.getElementById('clear-files');

    let selectedFiles = [];

    // File input change handler
    fileInput.addEventListener('change', function(e) {
        handleFiles(e.target.files);
    });

    // Drag and drop handlers
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.add('border-indigo-500', 'bg-indigo-50');
        dropZone.classList.remove('border-gray-300');
    });

    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.remove('border-indigo-500', 'bg-indigo-50');
        dropZone.classList.add('border-gray-300');
    });

    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.remove('border-indigo-500', 'bg-indigo-50');
        dropZone.classList.add('border-gray-300');

        const files = e.dataTransfer.files;
        handleFiles(files);
    });

    // Clear files handler
    clearFilesBtn.addEventListener('click', function() {
        selectedFiles = [];
        updateFileInput();
        updateDisplay();
    });

    function handleFiles(files) {
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        const maxSize = 10 * 1024 * 1024; // 10MB

        Array.from(files).forEach(file => {
            if (!validTypes.includes(file.type)) {
                alert(`File "${file.name}" is not a valid image type. Please select PNG, JPG, or JPEG files.`);
                return;
            }

            if (file.size > maxSize) {
                alert(`File "${file.name}" is too large. Maximum file size is 10MB.`);
                return;
            }

            // Check if file already selected
            const existingFile = selectedFiles.find(f => f.name === file.name && f.size === file.size);
            if (!existingFile) {
                selectedFiles.push(file);
            }
        });

        updateFileInput();
        updateDisplay();
    }

    function updateFileInput() {
        // Create a new DataTransfer object to update the file input
        const dt = new DataTransfer();
        selectedFiles.forEach(file => {
            dt.items.add(file);
        });
        fileInput.files = dt.files;
    }

    function updateDisplay() {
        if (selectedFiles.length === 0) {
            filePreview.classList.add('hidden');
            fileCounter.classList.add('hidden');
            return;
        }

        // Show counter
        fileCounter.classList.remove('hidden');
        fileCount.textContent = selectedFiles.length;

        // Show preview
        filePreview.classList.remove('hidden');

        // Update file list
        fileList.innerHTML = '';
        selectedFiles.forEach((file, index) => {
            const fileItem = createFileItem(file, index);
            fileList.appendChild(fileItem);
        });
    }

    function createFileItem(file, index) {
        const div = document.createElement('div');
        div.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg border';

        const sizeText = formatFileSize(file.size);

        div.innerHTML = `
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">${file.name}</p>
                    <p class="text-sm text-gray-500">${sizeText}</p>
                </div>
            </div>
            <button type="button" class="remove-file text-red-600 hover:text-red-800" data-index="${index}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        `;

        // Add remove file handler
        const removeBtn = div.querySelector('.remove-file');
        removeBtn.addEventListener('click', function() {
            selectedFiles.splice(index, 1);
            updateFileInput();
            updateDisplay();
        });

        return div;
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});
</script>
@endsection