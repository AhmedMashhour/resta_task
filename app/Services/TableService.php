<?php

namespace App\Services;

use Carbon\Carbon;

class TableService extends CrudService
{
    public function __construct()
    {
        parent::__construct("Table");
    }

    public  function getTablesAvailableForReservation(array $request, \stdClass &$output): void
    {
        $from = Carbon::parse($request['from_time'])->toDateTimeString();
        $to = Carbon::parse($request['to_time'])->toDateTimeString();
        $output->availableTable = $this->repository->getAvailableTableForReservation($from,$to,$request['number_of_guests'])->get();
    }

    // Implement your service methods here

}
