<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pricingoption extends Model
{
    use HasFactory;

    public string $adjust_methodUsed = '';

    public int $priceincentsAdjusted;
}
