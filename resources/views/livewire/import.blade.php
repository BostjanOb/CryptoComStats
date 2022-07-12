<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Import new file') }}
        </h2>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
            <div class="text-2xl">
                {{ __('Import a new file') }}
            </div>

            <form class="mt-6 text-gray-500">

                <div>
                    <label for="platform" class="block text-sm font-medium text-gray-700">Platform</label>
                    <select id="platform" name="platform"
                            wire:model.defer="platform"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="cdc">Crypto.com</option>
                        <option value="nexo">Nexo</option>
                    </select>
                </div>


                <div class="mt-4">
                    <label for="file" class="block text-sm font-medium text-gray-700">File</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input id="fle" type="file" name="file" wire:model="file" class="block w-full">
                    </div>
                    @error('file')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if($readyToImport > 0)
                    <div class="rounded-md bg-blue-50 p-4 mt-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1 md:flex md:justify-between">
                                <p class="text-sm text-blue-700">
                                    Ready to import {{ $readyToImport }} records
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <button type="button"
                        wire:click="import"
                        wire:loading.class="pointer-events-none animate-pulse"
                        {{ $readyToImport !== 0 ? '' : 'disabled' }}
                        class="{{ $readyToImport !== 0 ? '': 'opacity-50 pointer-events-none' }} inline-flex mt-4 items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Import file
                </button>
            </form>
        </div>
    </div>
</div>
