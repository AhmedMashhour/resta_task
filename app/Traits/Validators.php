<?php

namespace App\Traits;

trait Validators
{

    public function failMessages($laravelMessages) :array
    {
        $messages = [];

        foreach ($laravelMessages->toArray() as $key => $value)
        {
            $messages[] = $value[0];
        }

        return $messages;
    }

    public function arrayValidator($array , $singleValidator , \stdClass &$output) :array
    {
        if(is_null($array))
            $array = [];

        foreach ($array as $single) {
            return $this->objectValidator($single , $singleValidator , $output);
        }

        return [];
    }

    public function objectValidator($object , $validatorRule , \stdClass &$output) :array
    {
        if(is_null($object))
            $object = [];

        $validator = \Validator::make($object , $validatorRule);

        if ($validator->fails()) {
            if(isset($output->Error)) {
                $output->Error = array_merge($output->Error , $this->failMessages($validator->messages()));
            } else {
                $output->Error = $this->failMessages($validator->messages());
            }

            return [];
        } else {
            return $validator->validate();
        }
    }


}
