<?php

namespace App\Rules\Seller\Feed;

use Illuminate\Contracts\Validation\Rule;

class YoutubeURLRule implements Rule
{

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return (bool)preg_match('/(youtube.com|youtu.be)\/(embed)?(\?v=)?(\S+)?/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('messages.feed.validation.youtube_url_error');
    }
}
