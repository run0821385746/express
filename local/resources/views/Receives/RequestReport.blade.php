
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
            line-height: 10px;
            margin: -30px 5px -30px -30px;
        }
        table, tr, th, td{
            font-family: "THSarabunNew";
            border-collapse: collapse;
            width: 100%;
            border: 1px solid rgb(121, 120, 120);
            padding: 8px;
        }
        p { page-break-after: always; }
        .footer { position: fixed; bottom: -15px; left:-15px; }
        .pagenum:before { content: counter(page); }
    </style>

</head>
<body>
    @if (count($bookings) > 0)
    <div class="footer" style="bottom: 15px !important; left:0px !important; text-align:center;">
        <div>
            <span>signature____________________________inspector</span>
        </div>
        <div>
            <span>(____________________________)</span>
        </div>
    </div>
    <div class="footer">Page: <span class="pagenum"></span></div>
        <table width="100%" style="border-top:none; border-left:none; border-right:none;">
            <thead>
                <tr style="border:none;">
                    <th colspan="9" style="border:none; padding:0px;">
                        <div align="center" style="font-weight: bold; font-size:20px;">
                            SERVICE EXPRESS
                            <br>
                            REQUEST REPORT
                        </div>
                        <table style="border:none; margin-left:-8px; margin-right:-8px;">
                            <tr style="border:none;">
                                <th style="border:none; text-align:left;" valign="top" align="center">
                                    @if ($dateto != null)
                                        @php
                                            $datefrom = date_create("$datefrom");
                                            $dateto = date_create("$dateto");
                                        @endphp
                                        Between:
                                        <br>
                                        {{date_format($datefrom,"d/m/Y").' - '.date_format($dateto,"d/m/Y")}}
                                    @else
                                        @php
                                            $date = date_create($datefrom."-01");
                                        @endphp
                                        Month: {{date_format($date,"m/Y")}}
                                    @endif
                                </th>
                                <th style="border:none; text-align:left;" valign="top" align="center">
                                    Branch: {{$dropCenter->drop_center_name_initial}}
                                </th>
                                <th style="border:none; text-align:left;" valign="top" align="center">
                                    Print By: {{$employee->emp_firstname.' '.$employee->emp_lastname}}
                                </th>
                                <th style="border:none; text-align:left;" valign="top" align="center">
                                    Print Time: {{ date('d/m/Y H:i') }}
                                </th>
                            </tr>
                        </table>
                    </th>
                </tr>
            </thead>
            <thead>
                <tr style="background-color: #ccc4c4;">
                    <th valign="top">
                        <small>NO.</small>
                    </th>
                    <th valign="top">
                        <small>Recive Time.</small>
                    </th>
                    <th valign="top">
                        <small>Booking No.</small>
                    </th>
                    <th valign="top">
                        <small>Tracking No.</small>
                    </th>
                    <th valign="top">
                        <small>Number of parcel</small>
                    </th>
                    <th valign="top">
                        <small>Shipping_fee</small>
                    </th>
                    <th valign="top">
                        <small>COD_Totle</small>
                    </th>
                    <th valign="top">
                        <small>COD_fee</small>
                    </th>
                    <th valign="top">
                        <small>fee_Totle</small>
                    </th>
                </tr>
            </thead>
            <tbody>
                @php
                    $i = 0;
                    $sumparcel = 0;
                    $sumshipping_fee = 0;
                    $sumCOD_Totle = 0;
                    $sumCOD_fee = 0;
                    $sumfee_Totle = 0;
                @endphp
                @foreach ($bookings as $booking)
                    @php
                        $trackings = App\Model\Tracking::where('tracking_booking_id',$booking->id)->where('tracking_no','not like','%Destroy%')->where('tracking_no','!=','')->get();
                    @endphp
                    @foreach ($trackings as $tracking)
                        @php
                            $COD = 0;
                            $COD_fee = 0;
                            $i++;
                            $SubTrackings = App\Model\SubTracking::where('subtracking_tracking_id',$tracking->id)->get();
                            $countparcel = count($SubTrackings);
                            foreach ($SubTrackings as $SubTracking) {
                                $COD += $SubTracking->subtracking_cod;
                                $COD_fee += $SubTracking->subtracking_cod_fee;
                            }
                            $sumparcel += $countparcel;
                            $sumshipping_fee += $tracking->tracking_amount-$COD_fee;
                            $sumCOD_Totle += $COD;
                            $sumCOD_fee += $COD_fee;
                            $sumfee_Totle += $tracking->tracking_amount;
                        @endphp
                        <tr>
                            <td valign="top" align="center">
                                {{$i}}
                            </td>
                            <td valign="top" align="center">
                                @php
                                    $date = $booking->created_at;
                                    $timestring = $date->format('d-m-Y h:i');
                                @endphp
                                {{ $timestring }}
                            </td>
                            <td valign="top" align="center">
                                {{$booking->booking_no}}
                            </td>
                            <td valign="top" align="center">
                                {{$tracking->tracking_no}}
                            </td>
                            <td valign="top" align="right">
                                {{number_format($countparcel,2)}}
                            </td>
                            <td valign="top" align="right">
                                {{number_format($tracking->tracking_amount-$COD_fee,2)}}
                            </td>
                            <td valign="top" align="right">
                                {{number_format($COD,2)}}
                            </td>
                            <td valign="top" align="right">
                                {{number_format($COD_fee,2)}}
                            </td>
                            <td valign="top" align="right">
                                {{number_format($tracking->tracking_amount,2)}}
                            </td>
                        </tr>
                    @endforeach

                @endforeach
            </tbody>
            <tbody>
                <tr>
                    <td valign="top" align="center" colspan="4">
                        Totle
                    </td>
                    <td valign="top" align="right">
                        {{number_format($sumparcel,2)}}
                    </td>
                    <td valign="top" align="right">
                        {{number_format($sumshipping_fee,2)}}
                    </td>
                    <td valign="top" align="right">
                        {{number_format($sumCOD_Totle,2)}}
                    </td>
                    <td valign="top" align="right">
                        {{number_format($sumCOD_fee,2)}}
                    </td>
                    <td valign="top" align="right">
                        {{number_format($sumfee_Totle,2)}}
                    </td>
                </tr>
            </tbody>
        </table>
    @else
        <div align="center">
            ยังไม่มีรายการ
        </div>
    @endif
  {{-- <h2>daily report</h2> --}}
</body>
</html>
