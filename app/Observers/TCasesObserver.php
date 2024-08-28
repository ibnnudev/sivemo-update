<?php

namespace App\Observers;

class TCasesObserver
{
    public function creating($param)
    {
        if (auth()->check()) {
            $param->created_by = auth()->user()->id;
        }
    }

    public function updating($param)
    {
        if (auth()->check()) {
            $param->updated_by = auth()->user()->id;
        }
    }
}
