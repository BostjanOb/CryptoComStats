<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200">
        <div class="px-4 py-5 sm:px-6 flex flex-col items-center justify-between sm:flex-row">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Earnings
            </h3>
            <div class="flex items-center flex-col mt-2 space-x-2 sm:flex-row sm:mt-0">
                <button type="button"
                        wire:click="prevMonth"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 hidden md:block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"/>
                    </svg>
                </button>
                <input type="date" wire:model="from" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                <span>-</span>
                <input type="date" wire:model="to" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                <button type="button"
                        wire:click="nextMonth"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 hidden md:block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </button>
            </div>
        </div>
        <div>
            @if( $transactions->count() === 0 )
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No transactions</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Start by importing transactions.
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('import') }}"
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Import
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else

                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                &nbsp;
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Amount
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Native amount
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Current native amount
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rows as $row)
                                            @continue($row['amount'] === 0)
                                            <tr class="bg-white">
                                                <td class="font-bold px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $row['title'] }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <x-price :price="$row['amount'] ?? 0" currency="CRO"/>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <x-price :price="$row['native'] ?? 0" currency="EUR" :decimals="3"/>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <x-price :price="$row['currentNative'] ?? 0" currency="EUR" :decimals="3"/>
                                                </td>
                                            </tr>
                                        @endforeach

                                        @if(count($earnCdc))
                                            <tr class="bg-gray-200">
                                                <td class="font-bold px-6 py-4 whitespace-nowrap uppercase font-medium text-gray-900">
                                                    Earn
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                </td>
                                            </tr>
                                            @if(count($earnCdc))
                                                <tr class="bg-white">
                                                    <td class="font-bold px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                                        Crypto.com
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    </td>
                                                </tr>
                                                @foreach($earnCdc as $coin)
                                                    <tr class="bg-white">
                                                        <td class="font-semibold px-6 py-4 pl-12 whitespace-nowrap text-sm font-medium text-gray-900">
                                                            {{ $coin['title'] }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            <x-price :price="$coin['amount']" :currency="$coin['symbol']"/>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            <x-price :price="$coin['native']" currency="EUR" :decimals="3"/>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            <x-price :price="$coin['currentNative']" currency="EUR" :decimals="3"/>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif


                                            @if(count($earnNexo))
                                                <tr class="bg-gray-50">
                                                    <td class="font-bold px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                                        Nexo
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    </td>
                                                </tr>
                                                @foreach($earnNexo as $coin)
                                                    <tr class="bg-gray-50">
                                                        <td class="font-semibold px-6 py-4 pl-12 whitespace-nowrap text-sm font-medium text-gray-900">
                                                            {{ $coin['title'] }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            <x-price :price="$coin['amount']" :currency="$coin['symbol']"/>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            <x-price :price="$coin['currentNative']" currency="EUR" :decimals="3"/>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif


                                            <tr class="bg-gray-100">
                                                <td class="font-semibold px-6 py-4 pl-12 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    Total earn
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                </td>
                                                <td class="px-6 py-4 font-semibold whitespace-nowrap text-sm text-gray-500">
                                                </td>
                                                <td class="px-6 py-4 font-semibold whitespace-nowrap text-sm text-gray-500">
                                                    <x-price :price="$earnCdc->sum('currentNative') + $earnNexo->sum('currentNative')" currency="EUR" :decimals="3"/>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>

                                    <tfoot class="bg-blue-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-4 text-left font-bold text-gray-900 uppercase tracking-wider">
                                                Total earnings
                                            </th>
                                            <th scope="col" class="px-6 py-4 text-left font-bold text-gray-900 uppercase tracking-wider">
                                            </th>
                                            <th scope="col" class="px-6 py-4 text-left font-bold text-gray-900 uppercase tracking-wider">
                                            </th>
                                            <th scope="col" class="px-6 py-4 text-left font-bold text-gray-900 uppercase tracking-wider">
                                                <x-price :price="$sum['currentNative']"
                                                         currency="EUR" :decimals="3"/>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            @endif
        </div>
    </div>


</div>
