<?php
namespace app\packs;

interface IPack
{
    function encode($buffer);

    function decode($buffer);
}