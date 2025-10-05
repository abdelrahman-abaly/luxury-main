<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ScheduleTaskCollection extends ResourceCollection
{

    public function toArray(Request $request)
    {
        return $this->collection();
    }
}
