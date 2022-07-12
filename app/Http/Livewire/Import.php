<?php

namespace App\Http\Livewire;

use App\Imports\CdcTransactionsImport;
use App\Imports\NexoTransactionsImport;
use App\Platform;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Excel;

class Import extends Component
{
    use WithFileUploads;

    public int $readyToImport = 0;
    public string $platform = 'cdc';
    public $file;

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
        }
    }

    public function validateCdc()
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
            "timestamp_utc",
            "transaction_description",
            "currency",
            "amount",
            "to_currency",
            "to_amount",
            "native_currency",
            "native_amount",
            "native_amount_in_usd",
            "transaction_kind",
        ], $header))) {
            $this->addError('file', 'Invalid file format');

            return;
        }

        $this->readyToImport = count($data[0]);
    }

    public function import()
    {
        switch ($this->platform) {
            case Platform::CDC:
                (new CdcTransactionsImport(Auth::user()))->import($this->file->path(), null, Excel::CSV);
                break;
            case Platform::NEXO;
                (new NexoTransactionsImport(Auth::user()))->import($this->file->path(), null, Excel::CSV);
                break;
        }

        $this->file->delete();
        $this->file = null;
        $this->readyToImport = 0;
    }

    public function render()
    {
        return view('livewire.import');
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
            'date_time',
        ], $header))) {
            $this->addError('file', 'Invalid file format');

            return;
        }

        $this->readyToImport = count($data[0]);
    }
}
