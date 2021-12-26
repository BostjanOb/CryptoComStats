<?php

namespace App\Http\Livewire;

use App\Imports\TransactionsImport;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Excel;

class Import extends Component
{
    use WithFileUploads;

    public int $readyToImport = 0;
    public $file;

    public function updatedFile()
    {
        $this->readyToImport = 0;
        $this->validate(['file' => ['file']]);

        $data = null;
        try {
            $data = (new TransactionsImport(Auth::user()))->toArray($this->file->path(), null, Excel::CSV);
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
        (new TransactionsImport(Auth::user()))->import($this->file->path(), null, Excel::CSV);

        $this->file->delete();
        $this->file = null;
        $this->readyToImport = 0;
    }

    public function render()
    {
        return view('livewire.import');
    }
}
