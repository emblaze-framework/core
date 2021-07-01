<?php

namespace Emblaze\Validation;

/**
 * We use a https://github.com/rakit/validation
 * raki validation is a PHP Laravel like standalone validation library
 */
use Emblaze\Url\Url;
use Emblaze\Http\Request;
use Emblaze\Session\Session;
use Rakit\Validation\Validator;

class Validate
{
    /**
     * Validation constructor
     */
    private function __construct() {}

    /**
     * Validate request
     * 
     * @param array $rules
     * @param bool $json
     * 
     * @return mixed
     */
    public static function validate(array $rules, $json = true)
    {
        $validator = new Validator;

        // $validation = $validator->validate($_POST + $_FILES, [
        //     'name'                  => 'required',
        //     'email'                 => 'required|email',
        //     'password'              => 'required|min:6',
        //     'confirm_password'      => 'required|same:password',
        //     'avatar'                => 'required|uploaded_file:0,500K,png,jpeg',
        //     'skills'                => 'array',
        //     'skills.*.id'           => 'required|numeric',
        //     'skills.*.percentage'   => 'required|numeric'
        // ]);

        $validation = $validator->validate($_POST + $_FILES, $rules);

        $errors = $validation->errors();

        if ($validation->fails()) {
            if($json) {
                // return ['errors' => $errors->firstOfAll()];
                // return ['errors' => $errors->all()];
                return ['errors' => $errors->toArray()];
            } else {
                Session::set('errors', $errors);
                Session::set('old', Request::all());
                // redirect to previous
                return Url::redirect(Url::previous());
            }
        }
    }
}