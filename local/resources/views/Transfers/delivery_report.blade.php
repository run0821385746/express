
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
            @font-face {
                font-family: 'THSarabunNew';
                font-style: normal;
                font-weight: normal;
                src: url("{{ public_path('fonts/THSarabunNew.ttf') }}") format('truetype');
            }
            @font-face {
                font-family: 'THSarabunNew';
                font-style: normal;
                font-weight: bold;
                src: url("{{ public_path('fonts/THSarabunNew Bold.ttf') }}") format('truetype');
            }
            @font-face {
                font-family: 'THSarabunNew';
                font-style: italic;
                font-weight: normal;
                src: url("{{ public_path('fonts/THSarabunNew Italic.ttf') }}") format('truetype');
            }
            @font-face {
                font-family: 'THSarabunNew';
                font-style: italic;
                font-weight: bold;
                src: url("{{ public_path('fonts/THSarabunNew BoldItalic.ttf') }}") format('truetype');
            }
            body {
                font-family: "THSarabunNew";
                padding: -10px -25px 20px -25px;
            }

            #customers {
            font-family: "THSarabunNew";
            border-collapse: collapse;
            width: 100%;
            }
            
            #customers td, #customers th {
            border: 1px solid rgb(121, 120, 120);
            padding: 8px;
            }
            
            #customers tr:nth-child(even){background-color: #ffffff;}
            
            #customers tr:hover {background-color: #ccc;}
            
            #customers th {
            padding-top: 3px;
            padding-bottom: 3px;
            text-align: left;
            background-color: #ccc4c4;
            color: #000000;
            }

            .myDiv {   
            font-family: "THSarabunNew";
            font-size: 26px;
            text-align: center;
            font-weight: bold;
            line-height: 16px;
            }

            .myDCCODE {   
            font-family: "THSarabunNew";
            font-size: 26px;
            text-align: center;
            border-style: solid;
            }
            div.c {
            white-space: nowrap; 
            width: 100px; 
            overflow: hidden;
            }
            div.newline {
            margin-top: -5px;
            width: 180px;
            overflow: visible;
            line-height: 8px;
            }
            p { page-break-after: always; }
            .footer { position: fixed; bottom: 50px; }
            .pagenum:before { content: counter(page); }
        </style>
    </head>
    <body>
        @php
            $user = Auth::user();
            $employee = App\Model\Employee::find($user->employee_id);
            $date = date("d/m/Y");
        @endphp
        <div class="col-lg-12">
            <table id="customers" style="line-height: 8px; max-width:100%; width:100%;">
                <thead>
                    <tr style="border-top:none;">
                        <th valign="top" colspan="8" style="padding: 0px; border:none;">
                            <table width="100%" style="line-height: 5px; border-collapse: collapse; border:none;">
                                <tbody>
                                    <tr>
                                        <td colspan="5" style="border-collapse: collapse; border:none; background-color:#fff; padding:-20px -20px;">
                                            <div class="myDiv">
                                                <h4>SERVICE EXPRESS <br> DELEVERY RECORD</h4>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border-collapse: collapse; border:none; background-color:#fff; padding-top:0px; padding-bottom:0px;">
                                            <b>Batch Date:</b> {{$date}}
                                        </td>
                                        <td style="border-collapse: collapse; border:none; background-color:#fff; padding-top:0px; padding-bottom:0px; width:150px;">
                                            <b>Truck ID: </b>{{$TranserBill->tranfer_driver_sender_numberplate}}
                                        </td>
                                        <td style="border-collapse: collapse; border:none; background-color:#fff; padding-top:0px; padding-bottom:0px;">
                                            <b>Driver Name:</b> {{ mb_substr($currier->emp_firstname." ".$currier->emp_lastname,0,22, "utf-8")}}
                                        </td>
                                        <td style="border-collapse: collapse; border:none; background-color:#fff; padding-top:0px; padding-bottom:0px;">
                                            <b>Tel:</b> {{$currier->emp_phone}} 
                                        </td>
                                        <td style="border-collapse: collapse; border:none; background-color:#fff; padding-top:0px; padding-bottom:0px;">
                                            <div class="myDCCODE" style="padding-top: 9px; padding-bottom: 2px;">
                                                <b>{{$dropCenter->drop_center_name_initial}}</b>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border-collapse: collapse; border:none; background-color:#fff; line-height:2px;">
                                            <b>Print By:</b> {{ substr($employee->emp_firstname,0,16) }} 
                                        </td>
                                        <td style="border-collapse: collapse; border:none; background-color:#fff; line-height:2px;">
                                            <b>On:</b> {{date("d/m/Y H:i")}}
                                        </td>
                                        <td style="border-collapse: collapse; border:none; background-color:#fff; line-height:2px;"></td>
                                        <td style="border-collapse: collapse; border:none; background-color:#fff; line-height:2px;"></td>
                                        <td style="border-collapse: collapse; border:none; background-color:#fff; line-height:2px;">
                                            <b>Batch No:</b> {{$TranserBill->transfer_bill_no}}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </th>
                    </tr>
                </thead>
                <thead>
                    <tr>
                        <th valign="top" width="3%">
                            no
                        </th>
                        <th valign="top" width="10%" style="font-size: 12px !important; line-height: 6px;">
                            <small>Con No.#<br></small>
                            <small>Con Ref#<br></small>
                            <small>Customer Ref#<br></small>
                            <small>Con Content-Type<br></small>
                            <small>ORG DEST</small>
                        </th>
                        <th valign="top" style="max-width: 20px; width:20px;">
                            <small>Est DEL Date</small>
                            <br>
                            <small>Postponse Date</small>
                            <br>
                            <small>Shipper</small>
                        </th>
                        <th valign="top" width="6%">
                            <small>Service</small>
                        </th>
                        <th valign="top" width="30%">
                            <small>Consignee Name</small>
                            <br>
                            <small>Address</small>
                        </th>
                        <th valign="top" width="8%">
                            COD
                            <br>
                            <small>HCR/INVR</small>
                        </th>
                        <th valign="top" width="4%">
                            <small>Total</small>
                            <br>
                            <small>SOP</small>
                        </th>
                        <th valign="top" width="40%" style="border-right: none !important;">
                            <small>POD<small>
                            <br>
                            <small>Signature</small>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = 1;
                    @endphp
                    @php
                        $total_cod = 0;
                        $tracking_amount = count($transfers) ;
                        $subtracking_amount = 0 ;
                    @endphp
                    @foreach ($transfers as $transfer)
                        <tr> 
                            <td valign="top">
                                {{$i++}}
                            </td>
                            <td valign="top">
                                @php
                                
                                    $tracking = App\Model\Tracking::where('id',$transfer->transfer_tracking_id)->first();
                                    $booking_owner = App\Model\Booking::where('id',$tracking->tracking_booking_id)->first();
                                    $dropcenter_owner = App\Model\DropCenter::where('id',$booking_owner->booking_branch_id)->first();
                                    $receiveName = App\Model\Customer::where('id',$tracking->tracking_receiver_id)->first();
                                    $PostCode = App\Model\PostCode::where('postcode',$receiveName->cust_postcode)->first();
                                    $dropcenter_recive = App\Model\DropCenter::where('id',$PostCode->drop_center_id)->first();
                                @endphp
                                <b>{{$tracking->tracking_no}}</b>  <br><br>
                                {{$dropcenter_owner->drop_center_name_initial}} - {{$dropcenter_recive->drop_center_name_initial}}

                            </td>
                            <td valign="top" style="border-right: none !important;">
                                @php
                                    $senderName = App\Model\Customer::where('id',$tracking->booking->booking_sender_id)->first();
                                @endphp
                                    <small>{{date_format($tracking->booking->created_at,"d/m/Y H:i")}}</small>
                                    <br>
                                    <br>
                                    <div class="c">{{$senderName->cust_name}}</div>
                            </td>
                            <td valign="top" style="border-right: none !important; border-left: none !important;">
                                <b>ND</b>
                            </td>
                            <td valign="top" style="border-right: none !important; border-left: none !important; font-size:14px;">
                                <b>{{$receiveName->cust_name}} </b><br>
                                    {{$receiveName->cust_address}}
                                    {{$receiveName->District->name_th}}
                                    {{$receiveName->amphure->name_th}}
                                    {{$receiveName->province->name_th}}
                                    {{$receiveName->cust_postcode}}
                                <b> T:  {{$receiveName->cust_phone}}</b>
                            </td>
                            <td align="right" valign="top" style="border-right: none !important; border-left: none !important;">
                                @php
                                    $subTrackingList = App\Model\SubTracking::where('subtracking_tracking_id',$tracking->id)->get();
                                    $cod_amount = 0;
                                    foreach ($subTrackingList as $subTracking) {
                                        $cod_amount += $subTracking->subtracking_cod;
                                        $subtracking_amount += 1;
                                    }
                                    $total_cod += $cod_amount;
                                @endphp 
                                @if ($cod_amount > 0)
                                    <b> {{number_format($cod_amount, 2)}}</b>
                                @endif
                            </td>
                            <td align="right"  valign="top" style="border-right: none !important; border-left: none !important;">
                                @php
                                    $subTrackingListforshow = count($subTrackingList);
                                @endphp
                                {{$subTrackingListforshow}}<br>
                                {{$subTrackingListforshow}}
                            </td>
                            <td valign="top" style="border-left: none !important; border-right: none !important;">
                                <small>Signature</small> 
                                <table width="100%" style="line-height: 6px; padding:0px !important;">
                                    <tr style="border:none; ">
                                        <td style="border:none; padding:2px 0px 0px 0px !important;">
                                            @if ($transfer->transfer_status == 'CustomerResiveDone' || $transfer->transfer_status == 'CustomerResiveDoneReturn')
                                                <span style="color: red; font-weight:bold; font-size:14px;">
                                                    POD
                                                </span>
                                            @elseif($transfer->transfer_status == 'ReturnBackToDC')
                                                <span style="color: red; font-weight:bold; font-size:14px;">
                                                    Get back to DC
                                                </span>
                                            @else
                                                &nbsp;
                                            @endif
                                        </td>
                                    </tr>
                                    <tr style="border:none; padding:0px !important;">
                                        <td style="border-left:none; border-right:none; border-bottom:none; padding:0px !important;">
                                            @if ($transfer->transfer_status == 'CustomerResiveDone' || $transfer->transfer_status == 'ReturnBackToDC')
                                                @php
                                                    $cutrecivetime = substr($transfer->updated_at, 8,2).'/';
                                                    $cutrecivetime .= substr($transfer->updated_at, 5,2).' ';
                                                    $cutrecivetime .= substr($transfer->updated_at, 11,5);
                                                @endphp
                                                <span style="font-size:10px;">
                                                    Time: <span style="color: red;">{{ $cutrecivetime }}</span>
                                                </span>
                                            @else
                                                <span style="font-size:10px;">
                                                    Time:
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endforeach
                {{-- <tr>
                    <td></td>
                    <td></td>
                    <td colspan="6"></td>
                </tr> --}}
                <tr style="border:none;">
                    <td colspan="5" style="border:none;" valign="top" align="right">
                        <b>Totle COD</b>
                    </td>
                    <td style="border:none;" valign="top" align="right">
                        <b>{{number_format($total_cod,2)}}</b>
                    </td>
                    <td colspan="2" style="border:none;">
                        <table width="100%" style="line-height: 6px; padding:0px !important;">
                            <tr style="border:none; padding:0px !important;">
                                <td style="border:none; padding:0px !important;">
                                    <b>Total:</b>
                                </td>
                                <td style="border:none; padding:0px !important;">
                                    <b>{{$tracking_amount}}</b>
                                </td>
                                <td style="border:none; padding:0px !important;">
                                    <b>Con(s)</b>
                                </td>
                            </tr>
                            <tr style="border:none; padding:0px !important;">
                                <td style="border:none; padding:0px !important;">
                                    
                                </td>
                                <td style="border:none; padding:0px !important;">
                                    <b>{{$subtracking_amount}}</b>
                                </td>
                                <td style="border:none; padding:0px !important;">
                                    <b>Box</b>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="footer">
            <div class="row">
                <table width="100%">
                    <tbody>
                        <tr>
                            <td width="0.01%" align="center">
                            </td>
                            <td width="33.33%" align="center">
                                Signature___________________Courier/Biker
                                <br>
                                Date:{{date("d-m-Y")}}  DVL Time: {{date("H-i")}}
                            </td>
                            <td width="33.33%" align="center">
                                <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($TranserBill->transfer_bill_no,'I25')}}" alt="barcode" width='100px' height='40px' />
                                <br>
                                <div style="margin-top:-15px;">
                                    สำหรับแสกน POD By Batch
                                </div>
                            </td>
                            <td width="33.33%" align="center">
                                Signature___________________Leader/Station <br>
                                Date:{{date("d-m-Y")}}  DVL Time: {{date("H:i")}}
                            </td>
                        </tr>  
                    </tbody>
                </table>
            </div>
        </div> 
    </body>
</html>
