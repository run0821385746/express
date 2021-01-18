
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
            .footer { position: absolute; bottom: -25px; }
            .pagenum:before { content: counter(page); }
            .report_title{
                font-size: 12px;
            }
            
            .report_subject{
                font-size: 18px;
            }
            
            .report_content{
                font-size: 16px;
            }

        </style>
    </head>
    <body>
        <div class="footer" style="position: fixed; top:-10px;"><span class="pagenum" style="position: absolute; right:5px;"></span></div>
        @php
            $date = date("d/m/Y");
        @endphp
        <div class="col-lg-12">
            <table id="customers" style="line-height: 8px; max-width:100%; width:100%;">
                <thead>
                    <tr style="border-top:none;">
                        <th valign="top" colspan="9" style="padding: 5px 0px 5px 0px; border:none; background-color:#fff;">
                            <table width="100%" style="line-height: 5px; border-collapse: collapse; border:none;">
                                <tbody>
                                    <tr>
                                        <td colspan="5" style="border-collapse: collapse; border:none; background-color:#fff; padding:-10px 15px;">
                                            <div class="myDiv">
                                                <h4>SERVICE EXPRESS <br>Parcel Delivery report</h4>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border-collapse: collapse; border:none; background-color:#fff; padding-top:0px; padding-bottom:0px;">
                                            <b>branch:</b> 
                                            @if ($dropcenter == '0')
                                                ทั้งหมด
                                            @else
                                                @php
                                                    // dd($dropcenter);
                                                    $DropCenter_detail = App\Model\DropCenter::find($dropcenter);
                                                    // dd($DropCenter_detail);
                                                @endphp
                                                {{$DropCenter_detail->drop_center_name_initial}}
                                            @endif
                                        </td>
                                        <td colspan="2" style="border-collapse: collapse; border:none; background-color:#fff; padding-top:0px; padding-bottom:0px; width:200px;">
                                            <b>Date : {{date("d/m/Y", strtotime($selectdateFrom)).' - '.date("d/m/Y", strtotime($selectdateTo))}}</b>
                                            {{-- <b>Date : {{date_format($selectdateFrom,"d/m/Y").' - '.date_format($selectdateTo,"d/m/Y")}}</b> --}}
                                        </td>
                                        <td style="border-collapse: collapse; border:none; background-color:#fff; padding-top:0px; padding-bottom:0px; ">
                                            <b>Print By: {{ $employee->emp_firstname }}</b> 
                                        </td>
                                        <td style="border-collapse: collapse; border:none; background-color:#fff; padding-top:0px; padding-bottom:0px;">
                                            <b>Print Time: {{ date('d/m/Y H:i') }}</b> 
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </th>
                    </tr>
                </thead>
                <thead>
                    <tr>
                        <th valign="top" style="font-size: 12px !important; line-height: 6px; padding:5px 10px; text-align:center;">
                            No
                        </th>
                        <th valign="top" style="font-size: 12px !important; line-height: 6px; padding:5px 10px; text-align:center;">
                            Booking No.
                        </th>
                        <th valign="top" style="font-size: 12px !important; line-height: 6px; padding:5px 10px; text-align:center;">
                            Tracking No.
                        </th>
                        <th valign="top" style="font-size: 12px !important; line-height: 6px; padding:5px 10px; text-align:center;">
                            วันเวลาดำเนินการ
                        </th>
                        <th valign="top" style="font-size: 12px !important; line-height: 6px; padding:5px 10px; text-align:center;">
                            จำนวนพัสดุ/กล่อง
                        </th>
                        <th valign="top" style="font-size: 12px !important; line-height: 6px; padding:5px 10px; text-align:center;">
                            ค่าจัดส่ง
                        </th>
                        <th valign="top" style="font-size: 12px !important; line-height: 6px; padding:5px 10px; text-align:center;">
                            COD
                        </th>
                        <th valign="top" style="font-size: 12px !important; line-height: 6px; padding:5px 10px; text-align:center;">
                            ดำเนินการโดย
                        </th>
                        <th valign="top" style="font-size: 12px !important; line-height: 6px; padding:5px 10px; text-align:center;">
                            หมายเหตุ
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = 1;
                        $count_qty = count($Tracking);
                        $count_box = 0;
                        $service_price_sum = 0;
                        $cod_sum = 0;
                        // dd($Tracking);
                    @endphp
                    @foreach ($Tracking as $index)
                        @php
                            // dd($index->booking->id);
                        @endphp
                        <tr>
                            <td valign="top" style="font-size: 12px !important; line-height: 6px; padding:5px 10px; text-align: center;">
                                {{$i++}}
                            </td>
                            <td valign="top" style="font-size: 12px !important; line-height: 6px; padding:5px 10px; text-align: center;">
                                {{ $index->tracking->booking->booking_no }}
                            </td>
                            <td valign="top" style="font-size: 12px !important; line-height: 6px; padding:5px 10px; text-align: center;">
                                @php
                                    $ParcelWrongs = App\Model\ParcelWrongs::where('wrong_tracking_id', $index->transfer_dropcenter_tracking_id)->where('wrong_status', 'true')->first();
                                    if(!empty($ParcelWrongs)){
                                        if($index->created_at > $ParcelWrongs->created_at){
                                            $tracking_no = $index->tracking->tracking_no.'(RTN)';
                                        }else{
                                            $tracking_no = $index->tracking->tracking_no;
                                        }
                                    }else{
                                        $tracking_no = $index->tracking->tracking_no;
                                    }
                                    echo $tracking_no;
                                @endphp
                            </td>
                            <td valign="top" style="font-size: 12px !important; line-height: 6px; padding:5px 10px; text-align: center;">
                                {{date("d/m/Y H:i", strtotime($index->created_at))}}
                            </td>
                            <td valign="top" style="font-size: 12px !important; line-height: 6px; padding:5px 10px; text-align: center;">
                                @php
                                    echo $index->parcel_amount;
                                    $count_box += $index->parcel_amount;
                                @endphp
                            </td>
                            <td valign="top" style="font-size: 12px !important; line-height: 6px; padding:5px 10px; text-align: right;">
                                {{ number_format($index->tracking->tracking_amount,2) }}
                                @php
                                    $service_price_sum += $index->tracking->tracking_amount;
                                @endphp
                            </td>
                            <td valign="top" style="font-size: 12px !important; line-height: 6px; padding:5px 10px; text-align: right;">
                                @php
                                   $ParcelWrongs = App\Model\ParcelWrongs::where('wrong_tracking_id', $index->transfer_dropcenter_tracking_id)->where('wrong_status', 'true')->first();
                                    $SubTrackings = App\Model\SubTracking::where('subtracking_tracking_id', $index->transfer_dropcenter_tracking_id)->get();
                                    $COD = 0;
                                    if(!empty($ParcelWrongs)){
                                        if($index->created_at > $ParcelWrongs->created_at){
                                            foreach ($SubTrackings as $SubTracking) {
                                                    $COD += $SubTracking->subtracking_price;;
                                            }
                                        }else{
                                            foreach ($SubTrackings as $SubTracking) {
                                                $COD += $SubTracking->subtracking_cod;
                                            }
                                        }
                                    }else{
                                        foreach ($SubTrackings as $SubTracking) {
                                            $COD += $SubTracking->subtracking_cod;
                                        }
                                    }

                                    echo number_format($COD,2);
                                    $cod_sum += $COD;
                                @endphp
                            </td>
                            <td valign="top" style="font-size: 12px !important; line-height: 6px; padding:5px 10px; text-align: center;">
                                {{ $index->TransferDropCenterBill->Employee_sender->emp_firstname.' - '.$index->TransferDropCenterBill->Employee_driver->emp_firstname.' - '.$index->TransferDropCenterBill->Employee->emp_firstname }}
                            </td>
                            <td valign="top" style="font-size: 12px !important; line-height: 6px; padding:5px 10px; text-align: center;">
                                {{ $index->dc_sender->drop_center_name_initial.' - '.$index->dc_receiver->drop_center_name_initial }}
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td valign="top" colspan="4" style="font-size: 12px !important; line-height: 6px; padding:5px 10px; text-align: center;">
                            {{$count_qty}} รายการ
                        </td>
                        <td valign="top" style="font-size: 12px !important; line-height: 6px; padding:5px 10px; text-align: center;">
                            {{ $count_box }}
                        </td>
                        <td valign="top" style="font-size: 12px !important; line-height: 6px; padding:5px 10px; text-align: right;">
                            {{ number_format($service_price_sum,2) }}
                        </td>
                        <td valign="top" style="font-size: 12px !important; line-height: 6px; padding:5px 10px; text-align: right;">
                            {{ number_format($cod_sum,2) }}
                        <td valign="top" colspan="2" style="font-size: 12px !important; line-height: 6px; padding:5px 10px; text-align: center; background-color:#ccc;">
                            
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
                            </td>
                            <td width="33.33%" align="center">
                                Signature___________________<br>
                                Date:{{date("d/m/Y")}} Time: {{date("H:i")}}
                            </td>
                            <td width="33.33%" align="center">
                            </td>
                        </tr>  
                    </tbody>
                </table>
            </div>
        </div> 
    </body>
</html>
