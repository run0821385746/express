
<!DOCTYPE html>
<html lang="en">
<meta charset="UTF-8">
<head>
    
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <style type="text/css">
    @page { 
        margin: 0.5cm;
        size:Portrait; //tried portrait also. no effect.
    }
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
        padding:10px 15px;
        line-height: 18px;
    }

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
            font-weight: bold;
        }

        #customers {
        font-family: "THSarabunNew";
        border-collapse: collapse;
        width: 100%;
        }
        
        #customers td, #customers th {
        border: 1px solid #ddd;
        padding: 8px;
        }
        
        #customers tr:nth-child(even){background-color: #ffffff;}
        
        #customers tr:hover {background-color: #ddd;}
        
        #customers th {
        padding-top: 3px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #f2f2f2;
        color: #000000;
        }

        .myDiv {   
        font-family: "THSarabunNew";
        font-size: 26px;
        text-align: center;
        }

        .myDCCODE {   
        /* font-family: "THSarabunNew"; */
        /* font-size: 26px; */
        /* text-align: center; */
        /* border-style: solid; */
        }

        img{
            padding-left: 20px;
        }

        .textAlignVer{
            display:block;
            filter: flipv fliph;
            -webkit-transform: rotate(90deg); 
            -moz-transform: rotate(90deg); 
            transform: rotate(90deg); 
            position:relative;
            width:20px;
            white-space:nowrap;
            font-size:12px;
            margin-bottom:10px;
        }
    </style>

</head>
@foreach ($trackings as $tracking)
    @php
        $subTrackings = App\Model\SubTracking::where('subtracking_tracking_id', $tracking->id)->get();
        $allparcel = count($subTrackings);
        $parcelNo = 0;
    @endphp
    @foreach ($subTrackings as $subTracking)
        <body>
            <div style="height:650px; width:430px; background-color:#eee; position: absolute; top:7px; left:10px; z-index:-1; border-radius:10px;"></div>
            @if ($subTracking->subtracking_cod != '0')
                <div class="textAlignVer" style="position: absolute; top:360px; left:380px; border-radius:10px; font-size:40px; font-weight: bold;">เก็บเงินค่าสินค้า COD</div>
            @endif
            @php
                $date_time = date('d/m/Y H:i', strtotime($booking->created_at));
            @endphp
            <div class="textAlignVer" style="position: absolute; top:360px; left:690px; border-radius:10px; font-size:28px; font-weight: bold;">วันเวลาที่ส่ง {{$date_time}}</div>
            <div style="height:650px; width:290px; background-color:#eee; position: absolute; top:7px; right:10px; z-index:-1; border-radius:10px;"></div>
            @php
                $parcelNo++;
                // $user = Auth::user();
                $employee = App\Model\Employee::find($courier_id);
                $DimensionHistory = App\Model\DimensionHistory::where('dimension_history_subtracking_id',$subTracking->id)->first();
                $Customersender = App\Model\Customer::find($booking->booking_sender_id);
                $Customer = App\Model\Customer::find($tracking->tracking_receiver_id);
                $PostCode = App\Model\PostCode::where('postcode', $Customer->cust_postcode)->first();
                $DropCenterrecive = App\Model\DropCenter::find($PostCode->drop_center_id);
                $amphure = App\Model\amphure::find($Customer->cust_district);
                $date = date("d-m-Y");

                $trackingNo = $tracking->tracking_no;
            @endphp
            <table width="100%" style="border: none;">
                <tr style="border: none;">
                    <td width="60%" style="border: none; height: 600px; max-height:600px; max-width:30px;" align="left">
                        <div style="font-size:40px; position: absolute; top:-120px; left:250px; line-height:40px;" class="textAlignVer">
                            <span style="color: rgb(46, 46, 46); font-size:30px; line-height:90px;">
                                ชื่อที่อยู่ผู้รับ
                            </span>
                            <br>
                            &nbsp;&nbsp;{{ mb_substr($tracking->receiver->cust_name, 0, 25,'UTF-8') }} <br>
                            &nbsp;&nbsp;{{$tracking->receiver->cust_address}} {{$tracking->receiver->District->name_th}} 
                            <br>
                            &nbsp;&nbsp;{{$tracking->receiver->amphure->name_th}} {{$tracking->receiver->province->name_th}} 
                            
                            {{-- <span style="color: rgb(102, 101, 101); font-size:30px;">
                                เบอร์มือถือ
                            </span> --}}
                            <div style="line-height:40px;">
                                <table width="100%;" style="border: none; margin-left:-30px;">
                                    <tr style="border: none;">
                                        <td style="border: none; line-height:40px; padding-top:0px; padding-bottom:0px; padding-left:50px; padding-right:50px;" align="center">
                                            <span style="color: rgb(102, 101, 101); font-size:30px; position:relative;">
                                                เบอร์มือถือ
                                            </span>
                                            <br>
                                            <span style="font-size:50px; font-weight: bold;">
                                                {{$tracking->receiver->cust_phone}}
                                            </span>
                                        </td>
                                        <td style="border: none; line-height:40px; padding:0px;" align="center">
                                            <span style="color: rgb(102, 101, 101); font-size:30px;">
                                                รหัสไปรษณีย์
                                            </span>
                                            <br>
                                            <span style="font-size:80px; font-weight: bold;">
                                                {{$tracking->receiver->cust_postcode}}
                                            </span>
                                        </td>
                                    </tr>
                                </table>                                
                            </div>
                        </div>
                    </td>
                    <td width="40%" style="border: none; height: 600px; max-height:600px; max-width:30px;" align="left">
                        <div style="font-size:30px; position: absolute; top:-30px; left:570px; line-height:20px;" class="textAlignVer">
                            <span style="color: rgb(102, 101, 101); font-size:30px;">
                                ชื่อที่อยู่ผู้ส่ง
                            </span>
                            <br>
                            <table width="100%;" style="border: none;">
                                <tr style="border: none;">
                                    <td style="border: none; padding:0px; line-height:12px;">
                                        {{ mb_substr($Customersender->cust_name, 0, 25,'UTF-8') }}
                                    </td>
                                    <td style="border: none;">&nbsp;</td>
                                    <td style="border: none;">&nbsp;</td>
                                    <td style="border: none; padding:0px; line-height:12px; font-size:32px;" align="right"></td>
                                </tr>
                            </table>
                            <div></div>
                            &nbsp;&nbsp;{{$dropCenter->drop_center_address}}
                            {{$dropCenter->District->name_th}} {{$dropCenter->District->name_th}} 
                            <br>
                            <br>
                            &nbsp;&nbsp;{{$dropCenter->amphure->name_th}} 
                            {{$dropCenter->province->name_th}} 
                            
                            {{-- <span style="color: rgb(102, 101, 101); font-size:20px;">
                                เบอร์มือถือ
                            </span> --}}
                            <div style="line-height:22px;">
                                <table width="100px;" style="border: none;">
                                    <tr style="border: none;">
                                        <td style="border: none; line-height:40px; padding:0px;">
                                            <span style="color: rgb(102, 101, 101); font-size:20px;">
                                                รหัสไปรษณีย์
                                            </span>
                                            <span style="font-size:50px; font-weight: bold;">
                                                {{$dropCenter->drop_center_postcode}}
                                            </span>
                                        </td>
                                        <td style="border: none; line-height:40px; padding:0px;">
                                            <span style="color: rgb(102, 101, 101); font-size:20px; position:relative;">
                                                เบอร์มือถือ
                                            </span>
                                            <span style="font-size:50px; font-weight: bold;">
                                                {{$Customersender->cust_phone}} 
                                            </span>
                                        </td>
                                    </tr>
                                </table>                                
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
            <div style="font-size:45px;">
                Tracking No
            </div>
            <div style="font-size:110px; line-height:50px;" align="center">
                &nbsp;{{$trackingNo}}
                <span style="position: absolute; bottom:-600px; left:30px; font-size:30px;" class="textAlignVer">
                    Qty: {{$parcelNo.'/'.$allparcel}}
                </span>
            </div>
            <div align="center" style="margin-left:-20px;">
                <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($trackingNo.$subTracking->subtracking_under_tracking_id,'C39')}}" alt="barcode" width='80%' height='230px' />
            </div>
            {{-- <div class="row">
                <div class="col-md-8" style="border: 1px solid #000; max-height:600px; height:600px;">
                    
                </div>
                <div class="col-md-4" style="border: 1px solid #000; max-height:600px; height:600px;">

                </div> --}}
            </div>
            {{-- <div align="right" style="margin-top:-25px;">
                <span style='font-size:20px;'>Tel: (02)007-4755 </span>
            </div>
            <div align="center" style='font-size:40px; margin-top:5px; font-weight: bold;'>
                <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($trackingNo.$subTracking->subtracking_under_tracking_id,'C39')}}" alt="barcode" width='90%' height='65px' />
                <div style='margin-top:-21px; font-weight: bold; font-size:25px;'>{{$trackingNo}} ({{$parcelNo.'/'.$allparcel}})</div>
            </div>
            <table width="100%" style="margin-top: -11px; margin-left:-25px; margin-right:-15px;">
                <tr>
                    <th colspan='2' align='left' style='font-size:30px; padding:10px 15px; font-weight: none;'><!--{{$dropCenter->drop_center_name_initial}}-->DST <span style='font-weight: bold;'>{{$DropCenterrecive->drop_center_name_initial.'-'.$amphure->name_th}}</span></th>
                </tr>
                <tr>
                    <td style='width:80% !important; font-size:24px; padding:10px 15px;'>
                        ผู้ส่ง: {{ mb_substr($Customersender->cust_name, 0, 25,'UTF-8') }} {{$Customersender->cust_phone}}  <br>
                        {{$dropCenter->drop_center_address}}
                        {{$dropCenter->District->name_th}} 
                        {{$dropCenter->amphure->name_th}} 
                        {{$dropCenter->province->name_th}} 
                        {{$dropCenter->drop_center_postcode}}
                    </td>
                    <td style='width:20% !important; background-color:#000; color:#fff; font-weight: bold; font-size:50px; padding:12px 12px;' align='center'><h3>A03</h3></td>
                </tr>
                <tr>
                    <td colspan='2' align='left' style='font-size:24px; font-weight: bold; padding:10px 15px;'>
                        @php
                            $tracking = App\Model\Tracking::where('id',$subTracking->subtracking_tracking_id)->first();
                        @endphp
                        ผู้รับ: {{ mb_substr($tracking->receiver->cust_name, 0, 25,'UTF-8') }}  <br>
                        {{$tracking->receiver->cust_address}}
                        {{$tracking->receiver->District->name_th}} 
                        {{$tracking->receiver->amphure->name_th}} 
                        {{$tracking->receiver->province->name_th}} 
                        {{$tracking->receiver->cust_postcode}}
                    </td>
                </tr>
                <tr>
                    <td colspan='2' style='padding:-10px; font-size:18px;'>
                        <table width="100%" style='border: none;'>
                            <tr style='border: none;'>
                                <td width="50%" style='border-left:none; border-top:none; border-bottom:none; padding:0px 15px; line-height:11px; padding-top:3px; padding-bottom:0px;'>
                                    <h3>
                                        Weight: {{$DimensionHistory->dimension_history_weigth/1000}}
                                        <br>
                                        LWH: {{$DimensionHistory->dimension_history_length}}*{{$DimensionHistory->dimension_history_width}}*{{$DimensionHistory->dimension_history_hight}}
                                    </h3>
                                </td>
                                <td width="50%" style='border: none; line-height:10px; padding-top:5px; padding-bottom:0px;  padding-left:2px; padding-right:2px; text-align:center;'>
                                    @if ($subTracking->subtracking_cod != '0')
                                        <h1 style="margin-left:-10px; margin-right:-10px;">
                                            เก็บเงินค่าสินค้า COD   
                                        </h1>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <table width="100%" style='border:none; font-size:30px; margin-top:-20px; margin-top: -11px; margin-left:-25px; margin-right:-25px; padding:0px;'>
                <tr>
                    <td width="10%" align="center" style='border:none;'>
                        <div style='background-color:#000; color:#fff; font-weight: bold; font-size:46px; padding:23px 10px 10px 10px;'>BG</div>
                    </td>
                    <td width="90%" style='border:none; padding-top:15px; padding-left:0px;' align='right'>
                        <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($trackingNo.$subTracking->subtracking_under_tracking_id,'C39')}}" alt="barcode" width='390px' height='55px' />
                        <div style='margin-top:0px; padding-right:0px; font-weight: bold; font-size:25px;'>({{$parcelNo.'/'.$allparcel}}) {{$trackingNo}}</div>
                    </td>
                </tr>
            </table>
            <table width="100%" style="margin-top: -11px; margin-left:-25px; margin-right:-3px;">
                <tr>
                    <td width="85%" style='font-size:22px;'>
                        @php
                            $tracking = App\Model\Tracking::where('id',$subTracking->subtracking_tracking_id)->first();
                        @endphp
                        ผู้รับ: {{ mb_substr($tracking->receiver->cust_name, 0, 25,'UTF-8') }} {{$tracking->receiver->cust_phone}} <br>
                        {{$tracking->receiver->cust_address}}
                        {{$tracking->receiver->District->name_th}} 
                        {{$tracking->receiver->amphure->name_th}} 
                        {{$tracking->receiver->province->name_th}} 
                        {{$tracking->receiver->cust_postcode}}
                    </td>
                    <td width="15%" align='center'>
                        <span style='position: absolute; top:-10px; width:100%; font-size:16px;'>ลายเซ็นต์ผู้รับ</span>
                    </td>
                </tr>
            </table> --}}
            {{-- <div align="right">
                <span style='font-size:20px;'>Tel: (02)007-4755 </span>
            </div> --}}
            {{-- <div align="center" style='font-size:25px; margin-top:10px; line-height: 18px;'>
                <div>รับฟรี ทุกชิ้นถึงบ้าน ทั่วไทย ตรวจสอบสถานะสินค้าที่</div>
                <div>www.service-express.co.th/check-tracking</div>
                <img src="data:image/png;base64,{{DNS2D::getBarcodePNG('https://www.service-express.co.th/check-tracking','QRCODE')}}" alt="barcode" width='130px' height='130px' style='margin-top:10px;' />
            </div>
            <div style="line-height: 10px; position: absolute; bottom:0px; left:5px;">
                ผู้ส่ง: {{ mb_substr($Customersender->cust_name, 0, 25,'UTF-8') }}
                <br>
                ผู้รับ: {{ mb_substr($tracking->receiver->cust_name, 0, 25,'UTF-8') }}
                <br>
                ที่อยู่ผู้รับ: {{$tracking->receiver->amphure->name_th}} {{$tracking->receiver->province->name_th}}
                <br>
                เลขออเดอร์: {{$booking->booking_no}}
            </div>
            <div style="line-height: 10px; position: absolute; bottom:0px; right:5px;">
                <span style='font-size:20px;'>Tel: (02)007-4755 </span>
            </div> --}}
        </body>
    @endforeach
@endforeach
{{-- @php
    $allparcel = count($subTrackings);
    $parcelNo = 0;
@endphp
@foreach ($subTrackings as $subTracking)
<body>
    @php
        $parcelNo++;
        $user = Auth::user();
        $employee = App\Model\Employee::find($user->employee_id);
        $DimensionHistory = App\Model\DimensionHistory::where('dimension_history_subtracking_id',$subTracking->id)->first();
        $Customersender = App\Model\Customer::find($booking->booking_sender_id);
        $Customer = App\Model\Customer::find($tracking->tracking_receiver_id);
        $PostCode = App\Model\PostCode::where('postcode', $Customer->cust_postcode)->first();
        $DropCenterrecive = App\Model\DropCenter::find($PostCode->drop_center_id);
        $amphure = App\Model\amphure::find($Customer->cust_district);
        $date = date("d-m-Y");

        $trackingNo = $tracking->tracking_no;

        $website1 = 'shorturl.at/egHR3';
        $website = 'https://www.service-express.co.th';
    @endphp


    <div align="right" style="margin-top:-25px;">
        <span style='font-size:20px;'>Tel: (02)007-4755 </span>
    </div>
    <div align="center" style='font-size:40px; margin-top:5px; font-weight: bold;'>
        <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($trackingNo.$subTracking->id,'C39')}}" alt="barcode" width='90%' height='65px' />
        <div style='margin-top:-21px; font-weight: bold; font-size:25px;'>{{$trackingNo.'('.$subTracking->id.')'}} ({{$parcelNo.'/'.$allparcel}})</div>
    </div>
    <table width="100%" style="margin-top: -11px; margin-left:-25px; margin-right:-15px;">
        <tr>
            <th colspan='2' align='left' style='font-size:30px; padding:10px 15px; font-weight: none;'><!--{{$dropCenter->drop_center_name_initial}}-->DST <span style='font-weight: bold;'>{{$DropCenterrecive->drop_center_name_initial.'-'.$amphure->name_th}}</span></th>
        </tr>
        <tr>
            <td style='width:80% !important; font-size:24px; padding:10px 15px;'>
                ผู้ส่ง: {{ mb_substr($Customersender->cust_name, 0, 25,'UTF-8') }} {{$Customersender->cust_phone}}  <br>
                {{$dropCenter->drop_center_address}}
                {{$dropCenter->District->name_th}} 
                {{$dropCenter->amphure->name_th}} 
                {{$dropCenter->province->name_th}} 
                {{$dropCenter->drop_center_postcode}}
            </td>
            <td style='width:20% !important; background-color:#000; color:#fff; font-weight: bold; font-size:50px; padding:12px 12px;' align='center'><h3>A03</h3></td>
        </tr>
        <tr>
            <td colspan='2' align='left' style='font-size:24px; font-weight: bold; padding:10px 15px;'>
                @php
                    $tracking = App\Model\Tracking::where('id',$subTracking->subtracking_tracking_id)->first();
                @endphp
                ผู้รับ: {{ mb_substr($tracking->receiver->cust_name, 0, 25,'UTF-8') }} {{$tracking->receiver->cust_phone}} <br>
                {{$tracking->receiver->cust_address}}
                {{$tracking->receiver->District->name_th}} 
                {{$tracking->receiver->amphure->name_th}} 
                {{$tracking->receiver->province->name_th}} 
                {{$tracking->receiver->cust_postcode}}
            </td>
        </tr>
        <tr>
            <td colspan='2' style='padding:-10px; font-size:18px;'>
                <table width="100%" style='border: none;'>
                    <tr style='border: none;'>
                        <td width="50%" style='border-left:none; font-weight: bold; border-top:none; border-bottom:none; padding:10px 15px; line-height:11px; padding-top:3px; padding-bottom:0px;'>
                            Weight: {{$DimensionHistory->dimension_history_weigth/1000}}
                            <br>
                            LWH: {{$DimensionHistory->dimension_history_length}}*{{$DimensionHistory->dimension_history_width}}*{{$DimensionHistory->dimension_history_hight}}
                        </td>
                        <td width="50%" style='border: none; line-height:10px; padding-top:3px; padding-bottom:0px; text-align:center;'>
                            @if ($subTracking->subtracking_cod != '0')
                                <strong style="font-size:25px;">
                                    เก็บเงินค่าสินค้า COD   
                                </strong>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table width="100%" style='border:none; font-size:30px; margin-top:-20px; margin-top: -11px; margin-left:-25px; margin-right:-25px; padding:0px;'>
        <tr>
            <td width="10%" align="center" style='border:none;'>
                <div style='background-color:#000; color:#fff; font-weight: bold; font-size:46px; padding:23px 10px 10px 10px;'>BG</div>
            </td>
            <td width="90%" style='border:none; padding-top:15px; padding-left:0px;' align='right'>
                <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($trackingNo.$subTracking->id,'C39')}}" alt="barcode" width='390px' height='55px' />
                <div style='margin-top:0px; padding-right:0px; font-weight: bold; font-size:25px;'>({{$parcelNo.'/'.$allparcel}}) {{$trackingNo.'('.$subTracking->id.')'}}</div>
            </td>
        </tr>
    </table>
    
    <table width="100%" style="margin-top: -11px; margin-left:-25px; margin-right:-3px;">
        <tr>
            <td width="85%" style='font-size:22px;'>
                @php
                    $tracking = App\Model\Tracking::where('id',$subTracking->subtracking_tracking_id)->first();
                @endphp
                ผู้รับ: {{ mb_substr($tracking->receiver->cust_name, 0, 25,'UTF-8') }} {{$tracking->receiver->cust_phone}} <br>
                {{$tracking->receiver->cust_address}}
                {{$tracking->receiver->District->name_th}} 
                {{$tracking->receiver->amphure->name_th}} 
                {{$tracking->receiver->province->name_th}} 
                {{$tracking->receiver->cust_postcode}}
            </td>
            <td width="15%" align='center'>
                <span style='position: absolute; top:-10px; width:100%; font-size:16px;'>ลายเซ็นต์ผู้รับ</span>
            </td>
        </tr>
    </table>

    <div align="right">
        <span style='font-size:20px;'>Tel: (02)007-4755 </span>
    </div>
    <div align="center" style='font-size:25px; margin-top:10px; line-height: 18px;'>
        <div>รับฟรี ทุกชิ้นถึงบ้าน ทั่วไทย ตรวจสอบสถานะสินค้าที่</div>
        <div>www.service-express.co.th/check-tracking</div>
        <img src="data:image/png;base64,{{DNS2D::getBarcodePNG('https://www.service-express.co.th/check-tracking','QRCODE')}}" alt="barcode" width='130px' height='130px' style='margin-top:10px;' />
    </div>
    <div style="line-height: 10px; position: absolute; bottom:0px; left:5px;">
        ผู้ส่ง: {{ mb_substr($Customersender->cust_name, 0, 25,'UTF-8') }}
        <br>
        ผู้รับ: {{ mb_substr($tracking->receiver->cust_name, 0, 25,'UTF-8') }}
        <br>
        ที่อยู่ผู้รับ: {{$tracking->receiver->amphure->name_th}} {{$tracking->receiver->province->name_th}}
        <br>
        เลขออเดอร์: {{$booking->booking_no}}
    </div>
</body>
    
@endforeach --}}

</html>
<script src="http://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript" src="barcode/jquery/jquery-barcode.js"></script>
{{-- <script>
    $("#demo").barcode(
        "TH0304C2X2Q4B", // Value barcode (dependent on the type of barcode)
        "code39" // type (string)
    );
    $("#demo").css("width","270px");
    $("#demo > div").css("padding-left","0.35px");
    $("#demo > div").css("height","30px");
    
    $("#demo1").barcode(
        "TH0304C2X2Q4B", // Value barcode (dependent on the type of barcode)
        "code39" // type (string)
    );
    $("#demo1").css("width","270px");
    $("#demo1 > div").css("padding-left","0.35px");
    $("#demo1 > div").css("height","30px");
    
    $("#demo2").barcode(
        "TH0304C2X2Q4B", // Value barcode (dependent on the type of barcode)
        "code39" // type (string)
    );
    $("#demo2").css("width","270px");
    $("#demo2 > div").css("padding-left","0.35px");
    $("#demo2 > div").css("height","30px");
</script> --}}