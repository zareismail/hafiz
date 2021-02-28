<?php

namespace Zareismail\Hafiz\Models;
 
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Zareismail\NovaContracts\Models\AuthorizableModel as Model; 

class AuthorizableModel extends Model 
{
    use HasFactory, SoftDeletes; 
}
