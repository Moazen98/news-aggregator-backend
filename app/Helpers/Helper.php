<?php


function checkIsDevMode()
{
    return intval(env("DEV_MODE", 1));
}


function checkIsDevOrLocalMode()
{
    $envValue = env("DEV_MODE", 1);

    return ($envValue == 1 || $envValue == 2);
}



function checkIsLocalMode()
{
    $envValue = env("DEV_MODE", 1);

    return ($envValue == 2);
}

