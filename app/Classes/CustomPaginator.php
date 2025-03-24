<?php

namespace App\Classes;

use Illuminate\Pagination\LengthAwarePaginator;

class CustomPaginator extends LengthAwarePaginator
{

    public function toArray()
    {
        return [
            'data' => $this->items->toArray(),
            'info' => [
                'total' => $this->total(),
                'prev' => ($this->currentPage() > 1 ? ($this->currentPage() - 1) : null),
                'next' => ($this->hasMorePages() ? ($this->currentPage() + 1) : null),
                'text' => ($this->count() ? 'show ' . ($this->firstItem() != $this->lastItem() ? $this->firstItem() . ' to ' . $this->lastItem() : $this->lastItem()) . ' of ' . $this->total() . ' entries' : ''),
            ],
        ];
    }
}
