<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>


    {{--    <div>--}}
    {{--        <dl class="grid grid-cols-1 gap-5 sm:grid-cols-3">--}}
    {{--            <div class="px-4 py-5 bg-white shadow rounded-lg overflow-hidden sm:p-6">--}}
    {{--                <dt class="text-sm font-medium text-gray-500 truncate">--}}
    {{--                    <b>Earnings</b>--}}
    {{--                </dt>--}}
    {{--                <dd class="mt-1 text-3xl font-semibold text-gray-900">--}}
    {{--                    123,23 EUR--}}
    {{--                </dd>--}}
    {{--            </div>--}}

    {{--            <div class="px-4 py-5 bg-white shadow rounded-lg overflow-hidden sm:p-6">--}}
    {{--                <dt class="text-sm font-medium text-gray-500 truncate">--}}
    {{--                    <b>Cashback</b>--}}
    {{--                </dt>--}}
    {{--                <dd class="mt-1 text-3xl font-semibold text-gray-900">--}}
    {{--                    123,23 EUR--}}
    {{--                </dd>--}}
    {{--            </div>--}}

    {{--            <div class="px-4 py-5 bg-white shadow rounded-lg overflow-hidden sm:p-6">--}}
    {{--                <dt class="text-sm font-medium text-gray-500 truncate">--}}
    {{--                    <b>Earn</b> interest--}}
    {{--                </dt>--}}
    {{--                <dd class="mt-1 text-3xl font-semibold text-gray-900">--}}
    {{--                    123,23 EUR--}}
    {{--                </dd>--}}
    {{--            </div>--}}
    {{--        </dl>--}}
    {{--    </div>--}}

    <div class="bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200 mt-6">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Earnings
            </h3>
        </div>
        <div>
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
                                    <tr class="bg-white">
                                        <td class="font-bold px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            Cashback
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <x-price :price="$cashback['amount'] ?? 0" currency="CRO"/>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <x-price :price="$cashback['native'] ?? 0" currency="EUR" :decimals="3"/>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <x-price :price="$cashback['currentNative'] ?? 0" currency="EUR" :decimals="3"/>
                                        </td>
                                    </tr>

                                    <tr class="bg-gray-50">
                                        <td class="font-bold text-sm px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                            CRO Stake rewards
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <x-price :price="$croStake['amount'] ?? 0" currency="CRO"/>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <x-price :price="$croStake['native'] ?? 0" currency="EUR" :decimals="3"/>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <x-price :price="$croStake['currentNative'] ?? 0" currency="EUR" :decimals="3"/>
                                        </td>
                                    </tr>

                                    <tr class="bg-gray-50">
                                        <td class="font-bold px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                            Earn
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        </td>
                                    </tr>
                                    @foreach($earn as $coin)
                                        <tr class="bg-gray-50">
                                            <td class="font-semibold px-6 py-4 pl-12 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $coin['symbol'] }}
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
                                </tbody>

                                <tfoot class="bg-blue-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left font-bols text-gray-900 uppercase tracking-wider">
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left font-bols text-gray-900 uppercase tracking-wider">
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left font-bols text-gray-900 uppercase tracking-wider">
                                            <x-price :price="($cashback['native'] ?? 0) + ($croStake['native'] ?? 0) + $earn->sum('native')"
                                                     currency="EUR" :decimals="3"/>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left font-bols text-gray-900 uppercase tracking-wider">
                                            <x-price :price="($cashback['currentNative'] ?? 0) + ($croStake['currentNative'] ?? 0) + $earn->sum('currentNative')"
                                                     currency="EUR" :decimals="3"/>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
