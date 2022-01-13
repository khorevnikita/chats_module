<?php

namespace Khonik\Chats\Requests\Message;

use App\Http\Requests\ApiRequest;

class MessageRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => 'required|in:text,image,video,other',
            'body' => 'required|max:10000'
        ];
    }
}
