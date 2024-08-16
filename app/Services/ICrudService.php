<?php

namespace App\Services;

interface ICrudService{
    public function create(array $request ,\stdClass &$output) :void;

    public function update(array $request ,\stdClass &$output) :void;

    public function delete(array $request ,\stdClass &$output) :void;

    public function getAll(array $request ,\stdClass &$output) :void;

    public function getById(array $request ,\stdClass &$output) :void;

}
