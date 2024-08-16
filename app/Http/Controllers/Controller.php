<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    public function InternalDispatcher(Request $request): object
    {
        $returnedData = new \stdClass;

        preg_match("/[^\/]+$/", $request->url(), $matches);
        $internalRequest = $matches[0];
        if (!$internalRequest) {
            $returnedData->Error = ['something went wrong! please try again later'];
            return response()->json($returnedData, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            DB::connection()->getPdo()->beginTransaction();
            $code = null;
            call_user_func_array([$this, $internalRequest], [$request->all(), &$returnedData]);

            $output = new \stdClass;

            if (isset($returnedData->Error)) {
                DB::connection()->getPdo()->rollBack();

                if (isset($returnedData->code)) {
                    $code = $returnedData->code;
                    unset($returnedData->code);
                } else
                    $code = Response::HTTP_UNPROCESSABLE_ENTITY;


                $output->Error = $returnedData->Error;
            } else {
                if (isset($returnedData->code)) {
                    $code = $returnedData->code;
                    unset($returnedData->code);
                } else
                    $code = Response::HTTP_OK;

                $output->data = $returnedData;
                $output->current_datetime = Carbon::now()->toDateTimeString();

                if (isset($returnedData->no_need_commit) && $returnedData->no_need_commit) {
                    unset($output->data->no_need_commit);
                    DB::connection()->getPdo()->rollBack();
                } else {
                    if (isset($returnedData->no_need_commit))
                        unset($output->data->no_need_commit);
                    DB::connection()->getPdo()->commit();
                }
            }

            if (isset($returnedData->return_link))
                return $returnedData->return_link;

            return response()->json($output, $code);
        } catch (Exception $e) {
            Log::info($e);
            DB::connection()->getPdo()->rollBack();
            $returnedData->Error = ['something went wrong! please try again later', $e->getMessage()];
            return response()->json($returnedData, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

    }

}
