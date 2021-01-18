<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Booking;
use App\Exports\ExcelExportOrtherSale;
use App\Exports\ExcelExport;
use Excel;

class ExportExcelController extends Controller
{
    // function getBookingListToExportExcel() {
    //  $bookingList = Booking::get();
    //  return view('Receives.receive_excel',compact('bookingList'));
    // }

    // function exportBookingListToExcel() { 
    // $bookingList = Booking::get()->toArray();
    // $booking_array[] = array(
    //     'id',
    //     'booking_no',
    //     'booking_branch_id',
    //     'booking_sender_id',
    //     'booking_type',
    //     'booking_status',
    //     'booking_amount',
    //     'created_at'
    // );
    //     foreach($bookingList as $booking){
    //         return $booking;
    //         $booking_array[] = array(
    //             'id' => $booking->id,
    //             'booking_no' => $booking->booking_no,
    //             'booking_branch_id' => $booking->booking_branch_id,
    //             'booking_sender_id' => $booking->booking_sender_id,
    //             'booking_type' => $booking->booking_type,
    //             'booking_status' => $booking->booking_status,
    //             'booking_amount' => $booking->booking_amount,
    //             'created_at' => $booking->created_at
    //         );
    //     }

    //     Excel::create('Booking Data', function($excel) use ($bookingList){
    //         $excel->setTitle('Booking Data');
    //         $excel->sheet('Booking Data', function($sheet) use ($bookingList){
    //             $sheet->fromArray($bookingList, null, 'A1', false, false);
    //         });
    //     })->download('xlsx');
    // }


    public function export() 
    {
        $date = date('dmY');
        return Excel::download(new ExcelExport, 'bookingList'.$date.'.xlsx');
        // return Excel::download(new ExcelTrackingListExport, 'TrackingList.xlsx');
    }

    public function exportDayOrtherSale() 
    {
        $date = date('dmY');
        return Excel::download(new ExcelExportOrtherSale, 'OrtherSale'.$date.'.xlsx');
        // return Excel::download(new ExcelTrackingListExport, 'TrackingList.xlsx');
    }
    
}
