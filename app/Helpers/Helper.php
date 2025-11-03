<?php


function checkIsDevMode()
{
    return intval(env("DEV_MODE", 2));
}


function checkIsDevOrLocalMode()
{
    $envValue = env("DEV_MODE", 2);

    return ($envValue == 1 || $envValue == 2);
}



function checkIsLocalMode()
{
    $envValue = env("DEV_MODE", 2);

    return ($envValue == 2);
}

