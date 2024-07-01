<?php

namespace App\Http\Livewire;

use App\Imports\BinanceCardImport;
use App\Imports\BinanceEarnImport;
use App\Imports\CdcTransactionsImport;
use App\Imports\NexoTransactionsImport;
use App\Platform;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Excel;

class Import extends Component
{
    use WithFileUploads;

    public int $readyToImport = 0;

    public string $platform = 'cdc';

    public $file;

    public bool $imported = false;

    public function updatedFile()
    {
        $this->readyToImport = 0;
        $this->validate(['file' => ['file']]);

        switch ($this->platform) {
            case Platform::CDC:
                $this->validateCdc();
                break;
            case Platform::NEXO:
                $this->validateNexo();
                break;
            case Platform::BINANCE_EARN:
                $this->validateBinanceEarn();
                break;
            case Platform::BINANCE_CARD:
                $this->validateBinanceCard();
                break;
        }
    }

    public function import()
    {
        DB::transaction(function () {
            switch ($this->platform) {
                case Platform::CDC:
                    (new CdcTransactionsImport(Auth::user()))->import($this->file->path(), null, Excel::CSV);
                    break;
                case Platform::NEXO:
                    (new NexoTransactionsImport(Auth::user()))->import($this->file->path(), null, Excel::CSV);
                    break;
                case Platform::BINANCE_EARN:
                    (new BinanceEarnImport(Auth::user()))->import($this->file->path(), null, Excel::XLSX);
                    break;
                case Platform::BINANCE_CARD:
                    BinanceCardImport::import(Auth::user(), $this->file->path());
                    break;
            }

            $this->file->delete();
            $this->file = null;
            $this->readyToImport = 0;
            $this->imported = true;
        });
    }

    public function render()
    {
        return view('livewire.import');
    }

    private function validateCdc()
    {
        try {
            $data = (new CdcTransactionsImport(Auth::user()))->toArray($this->file->path(), null, Excel::CSV);
        } catch (\Exception $e) {
            $this->addError('file', $e->getMessage());

            return;
        }

        if (count($data) === 0) {
            $this->addError('file', 'No data found in the file');

            return;
        }

        $header = array_keys($data[0][0]);
        if (count(array_diff([
            'timestamp_utc',
            'transaction_description',
            'currency',
            'amount',
            'to_currency',
            'to_amount',
            'native_currency',
            'native_amount',
            'native_amount_in_usd',
            'transaction_kind',
        ], $header))) {
            $this->addError('file', 'Invalid file format');

            return;
        }

        $this->readyToImport = count($data[0]);
    }

    private function validateNexo()
    {
        try {
            $data = (new NexoTransactionsImport(Auth::user()))->toArray($this->file->path(), null, Excel::CSV);
        } catch (\Exception $e) {
            $this->addError('file', $e->getMessage());

            return;
        }

        if (count($data) === 0) {
            $this->addError('file', 'No data found in the file');

            return;
        }

        $header = array_keys($data[0][0]);

        if (count(array_diff([
            'type',
            'output_currency',
            'output_amount',
            'usd_equivalent',
            'date_time_utc',
        ], $header))) {
            $this->addError('file', 'Invalid file format');

            return;
        }

        $this->readyToImport = count($data[0]);
    }

    private function validateBinanceCard()
    {
        try {
            $data = json_decode(file_get_contents($this->file->path()));

            if ($data === null || ! property_exists($data, 'data')) {
                throw new \Exception('Invalid file');
            }

            $data = collect($data->data);
        } catch (\Exception $e) {
            $this->addError('file', $e->getMessage());

            return;
        }

        $this->readyToImport = $data->reject(fn ($t) => is_null($t->cashbackDetail))
            ->count();
    }

    private function validateBinanceEarn()
    {
        try {
            $data = (new BinanceEarnImport(Auth::user()))
                ->toArray($this->file->path(), null, Excel::XLSX);
        } catch (\Exception $e) {
            $this->addError('file', $e->getMessage());

            return;
        }

        if (count($data) === 0) {
            $this->addError('file', 'No data found in the file');

            return;
        }

        $header = array_keys($data[0][0]);
        if (count(array_diff([
            'dateutc',
            'product_name',
            'coin',
            'amount',
        ], $header))) {
            $this->addError('file', 'Invalid file format');

            return;
        }

        $this->readyToImport = count($data[0]);
    }
}
