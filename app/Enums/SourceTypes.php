<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class SourceTypes extends Enum
{
    const NEWS_API =   'news_api';
    const GUARDIAN_API =   'guardian';
    const NEW_YORK_API =   'new_york';
    const NEWS_AI_API =   'news_api_ai';
}
