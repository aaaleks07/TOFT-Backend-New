<?php
// src/Enum/VoteValue.php
namespace App\Enum;

enum VoteValue:int
{
    case DOWN = -1;
    case UP   = 1;
}
