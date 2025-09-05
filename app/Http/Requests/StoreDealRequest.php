<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDealRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'title'             => ['required', 'string', 'max:255'],
      'customer_id'       => ['nullable', 'exists:customers,id'],
      'lead_id'           => ['required', 'exists:leads,id'],
      'amount'            => ['required', 'numeric', 'min:0'],
      'expected_close_date' => ['nullable', 'date'],
      'pipeline_stage_id' => ['required', 'exists:pipeline_stages,id'],
      'notes'             => ['nullable','string'],

      'items'                 => ['required', 'array', 'min:1'],
      'items.*.product_id'    => ['required', 'exists:products,id'],
      'items.*.quantity'      => ['required', 'integer', 'min:1'],
      'items.*.unit_price'    => ['required', 'numeric', 'min:0'],
    ];
  }
}
