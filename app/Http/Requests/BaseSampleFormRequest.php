<?php 

namespace App\Http\Requests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

 abstract class BaseSampleFormRequest extends FormRequest
{
  protected $stopOnFirstFailure = true;   // whether to stop the request validation after first failure of any attribute
  public function authorize()
  {
    // whether the current user is allowed to access this request or not 
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
      'first_name' => 'required|max:50|string|min:2',
      'last_name' => 'required|max:50|string|min:2',
      'password' => 'required|min:8|max:100',
    ];
  }

  /**
   * Custom message for validation
   *
   * @return array
   */
  public function messages()
  {
    // what messages to return incase of a rule failure
    return [
      'email.exists' => 'This Email is Already Registered',
    ];
  }


  /**
   * To change the data in the form request after validatoion , this works for $request->all()
   * @return void
   */
  protected function passedValidation():void
    {
  // this function is always called in the formrequest, so if i unset somekey here i wont get it in validatedfunction because passedvalidation runs before validated() and make actual changes;
      $this->offunset() ; // to remove a key from request , 
      $this->merge([]);
    }


  /**
   * This data is passed to the rules method of the form request so if you want to change the data before passing to the rules method, do it here 
   * @return array
   */
  public function validationData() :array
  {
    return [];
    // return array_merge($this->all(),['email'=>'hamza.moeen@d4interactive.io']);
  }

  /**
   * This  method that is called incase of validation failure
   * @param mixed $validator
   */
  protected function failedValidation($validator)
  {
    parent::failedValidation($validator);
  }


  /**
   * This is called whenever the Authoirze method of form request return false
   * @throws \Illuminate\Auth\Access\AuthorizationException
   * @return never
   */
  public function failedAuthorization()
  {
    throw new AuthorizationException("You don't have the authority to update this post");
  }


  
  /**
   * This is after validation hook , if we want to manually add more checks , this runs after the above rules are checked but before the failedValidation method 
   * @param mixed $validator
   * @return void
   */
  public function withValidator($validator)
  {
      $validator->after(function ($validator) {
          // till here the above rules are checked and validator instance is updated with their errors etc if they have
          
         
      });
  }
}