<?php
// src/Enum/VoteValue.php
namespace App\Enum;

enum VoteValue:int
{
    case Down = -1;
    case Up   = 1;
}
