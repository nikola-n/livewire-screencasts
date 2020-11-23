<?php

namespace App\Http\Livewire;

use App\Http\Livewire\DataTable\WithBulkActions;
use App\Http\Livewire\DataTable\WithCachedRows;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Http\Livewire\DataTable\WithSorting;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithPerPagePagination, WithSorting, WithBulkActions, WithCachedRows;

    public $showDeleteModal = false;

    public $showEditModal = false;

    public $showFilters = false;

    public $filters = [
        'search'     => '',
        //to be on select option
        'status'     => '',
        'amount-min' => null,
        'amount-max' => null,
        'date-min'   => null,
        'date-max'   => null,
    ];

    public Transaction $editing;

    protected $queryString = [];

    protected $listeners = ['refreshTransactions' => '$refresh'];

    public function mount()
    {
        return $this->editing = $this->makeBlankTransaction();
    }
    //you can add a option in the select and you can save it and we dont want that
    //to prevent it: we use function and than get the keys from Transaction collection
    public function rules()
    {
        return [
            'editing.title'            => 'required',
            'editing.amount'           => 'required',
            'editing.status'           => 'required|in:' . collect(Transaction::STATUSES)->keys()->implode(','),
            'editing.date_for_editing' => 'required',
        ];
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function exportSelected()
    {
        return response()->streamDownload(function () {
            echo $this->selectedRowsQuery->toCsv();
        }, 'transactions.csv');
    }

    public function deleteSelected()
    {
        $this->selectedRowsQuery->delete();

        $this->showDeleteModal = false;

        //$transactions = $this->selectAll ?
        //    $this->transactionsQuery
        //    : $this->transactionsQuery->whereKey($this->selected);
        //
        //$transactions->delete();
    }

    public function makeBlankTransaction()
    {
        return Transaction::make(['created_at' => now(), ['status' => 'processing']]);
    }

    public function create()
    {
        $this->useCachedRows();
        //if it has a key (id) its in the database
        if ($this->editing->getKey()) {
            $this->editing = $this->makeBlankTransaction();
        }

        $this->showEditModal = true;
    }

    public function edit(Transaction $transaction)
    {
        $this->useCachedRows();

        if ($this->editing->isNot($transaction)) {
            $this->editing = $transaction;
        }
        $this->showEditModal = true;
    }

    public function toggleShowFilters()
    {
        $this->useCachedRows();
        $this->showFilters = ! $this->showFilters;
    }

    public function resetFilters()
    {
        $this->reset('filters');
    }

    public function save()
    {
        $this->validate();
        $this->editing->save();
        $this->showEditModal = false;
    }

    public function getRowsQueryProperty()
    {
        $query = Transaction::query()
            ->when($this->filters['status'], fn($query, $status) => $query->where('status', $status))
            ->when($this->filters['amount-min'], fn($query, $amount) => $query->where('amount', '>=', $amount))
            ->when($this->filters['amount-max'], fn($query, $amount) => $query->where('amount', '<=', $amount))
            ->when($this->filters['date-min'], fn($query, $date) => $query->where('created_at', '>=', Carbon::parse($date)))
            ->when($this->filters['date-max'], fn($query, $date) => $query->where('created_at', '<=', Carbon::parse($date)))
            ->when($this->filters['search'], fn($query, $search) => $query->where('title', 'like', '%' . $search . '%'));

        return $this->applySorting($query);
    }

    public function getRowsProperty()
    {
        return $this->cache(function () {
            return $this->applyPagination($this->rowsQuery);
        });
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'transactions' => $this->rows,
        ]);
    }
}
