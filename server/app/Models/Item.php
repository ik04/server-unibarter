<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Item extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        "name",
        "image",
        "price",
        "item_uuid",
        "description",
        "seller_user_id"
        // ? should i add tags/ discuss with the team


    ];
}