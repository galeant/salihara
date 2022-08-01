<?php

namespace App\Http\Requests\Admin\Misc;

use Illuminate\Foundation\Http\FormRequest;
use App\Misc;
use Symfony\Component\HttpKernel\Exception\HttpException;


class PartnerCreateRequest extends FormRequest
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
        $avail_type = Misc::PARTNER_TYPE;
        if (!in_array($this->type, $avail_type)) {
            throw new HttpException(404);
        }
        return [
            'logo' => 'required|array',
            'logo.*' => 'required'
        ];
    }
}
