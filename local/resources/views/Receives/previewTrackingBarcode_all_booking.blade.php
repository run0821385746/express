
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
        .ellipsis {
    overflow: hidden;
    height: 200px;
    line-height: 25px;
    margin: 20px;
    border: 5px solid #AAA;
}
.ellipsis:before {
    content: "";
    float: left;
    width: 5px;
    height: 200px;
}
.ellipsis > *:first-child {
    float: right;
    width: 100%;
    margin-left: -5px;
}
.ellipsis:after {
    content: "\02026";
    box-sizing: content-box;
    -webkit-box-sizing: content-box;
    -moz-box-sizing: content-box;
    float: right;
    position: relative;
    top: -25px;
    left: 100%;
    width: 3em;
    margin-left: -3em;
    padding-right: 5px;
    text-align: right;
    background-size: 100% 100%;/* 512x1 image,gradient for IE9. Transparent at 0% -> white at 50% -> white at 100%.*/
    ￿background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAgAAAAABCAMAAACfZeZEAAAABGdBTUEAALGPC/xhBQAAAwBQTFRF////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////AAAA////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////wDWRdwAAAP90Uk5TgsRjMZXhS30YrvDUP3Emow1YibnM9+ggOZxrBtpRRo94gxItwLOoX/vsHdA2yGgL8+TdKUK8VFufmHSGgAQWJNc9tk+rb5KMCA8aM0iwpWV6dwP9+fXuFerm3yMs0jDOysY8wr5FTldeoWKabgEJ8RATG+IeIdsn2NUqLjQ3OgBDumC3SbRMsVKsValZplydZpZpbJOQco2KdYeEe36BDAL8/vgHBfr2CvTyDu8R7esU6RcZ5ecc4+Af3iLcJSjZ1ivT0S/PMs3LNck4x8U7wz7Bv0G9RLtHuEq1TbJQr1OtVqqnWqRdoqBhnmSbZ5mXapRtcJGOc4t2eYiFfH9AS7qYlgAAARlJREFUKM9jqK9fEGS7VNrDI2+F/nyB1Z4Fa5UKN4TbbeLY7FW0Tatkp3jp7mj7vXzl+4yrDsYoVx+JYz7mXXNSp/a0RN25JMcLPP8umzRcTZW77tNyk63tdprzXdmO+2ZdD9MFe56Y9z3LUG96mcX02n/CW71JH6Qmf8px/cw77ZvVzB+BCj8D5vxhn/vXZh6D4uzf1rN+Cc347j79q/zUL25TPrJMfG/5LvuNZP8rixeZz/mf+vU+Vut+5NL5gPOeb/sd1dZbTs03hBuvmV5JuaRyMfk849nEM7qnEk6IHI8/qn049hB35QGHiv0yZXuMdkXtYC3ebrglcqvYxoj1muvC1nDlrzJYGbpcdHHIMo2FwYv+j3QAAOBSfkZYITwUAAAAAElFTkSuQmCC);
    background: -webkit-gradient(linear,left top,right top,
        from(rgba(255,255,255,0)),to(white),color-stop(50%,white));
        background: -moz-linear-gradient(to right,rgba(255,255,255,0),white 50%,white);
        background: -o-linear-gradient(to right,rgba(255,255,255,0),white 50%,white);
        background: -ms-linear-gradient(to right,rgba(255,255,255,0),white 50%,white);
        background: linear-gradient(to right,rgba(255,255,255,0),white 50%,white);
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
            @endphp
            <div align="right" style="margin-top:-25px;">
                <span style='font-size:20px;'>Tel: (02)007-4755 </span>
            </div>
            <div align="center" style='font-size:40px; margin-top:5px; font-weight: bold;'>
                <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($trackingNo.$subTracking->subtracking_under_tracking_id,'C39')}}" alt="barcode" width='90%' height='65px' />
                <div style='margin-top:-21px; font-weight: bold; font-size:25px;'>{{$trackingNo}} ({{$parcelNo.'/'.$allparcel}})</div>
            </div>
            <table width="100%" style="margin-top: -11px; margin-left:-25px; margin-right:-15px; max-width:470px">
                <tr>
                    <th colspan='2' align='left' style='font-size:30px; padding:10px 15px; font-weight: none;'><!--{{$dropCenter->drop_center_name_initial}}-->DST <span style='font-weight: bold;'>{{$DropCenterrecive->drop_center_name_initial.'-'.$amphure->name_th}}</span></th>
                </tr>
                <tr>
                    <td style='width:80% !important; font-size:24px; padding:10px 15px;'>
                        ผู้ส่ง: {{ mb_substr($Customersender->cust_name, 0, 25,'UTF-8') }} {{$Customersender->cust_phone}}
                        <div style="word-break: break-all; overflow: hidden; padding-top:-5px; padding-bottom:-10px; margin-top:0px; margin-right:-220px; max-width:390px; width:390px; height:50px; max-height:50px;">
                            {{$Customersender->cust_address}}
                            {{$Customersender->District->name_th}} 
                            {{$Customersender->amphure->name_th}} 
                            {{$Customersender->province->name_th}} 
                            {{$Customersender->cust_postcode}}
                        </div>
                    </td>
                    <td style='width:20% !important; background-color:#000; color:#fff; font-weight: bold; font-size:50px; padding:12px 12px;' align='center'><h3>A03</h3></td>
                </tr>
                <tr>
                    <td colspan='2' align='left' style='font-size:24px; font-weight: bold; padding:10px 15px; '>
                        {{-- <div style="border: 1px solid #ccc; margin:-10px -15px; word-break: break-all; max-width:470px;"> --}}
                            @php
                                $tracking = App\Model\Tracking::where('id',$subTracking->subtracking_tracking_id)->first();
                            @endphp
                            ผู้รับ: {{ mb_substr($tracking->receiver->cust_name, 0, 25,'UTF-8') }} {{$tracking->receiver->cust_phone}}
                            <div style="padding-top:-5px; padding-bottom:-5px; margin-top:0px; margin-right:-200px; max-width:440px; overflow: hidden; height:70px; max-height:70px;">
                                {{$tracking->receiver->cust_address}}
                                {{$tracking->receiver->District->name_th}} 
                                {{$tracking->receiver->amphure->name_th}} 
                                {{$tracking->receiver->province->name_th}} 
                                {{$tracking->receiver->cust_postcode}}
                            </div>
                        {{-- </div> --}}
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
                                <td width="50%" style='border: none; line-height:10px; padding-top:5px; padding-bottom:0px; padding-left:2px; padding-right:2px; text-align:center;'>
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
                        ผู้รับ: {{ mb_substr($tracking->receiver->cust_name, 0, 25,'UTF-8') }} {{$tracking->receiver->cust_phone}}
                        <div style="word-break: break-all; overflow: hidden; padding-top:-5px; padding-bottom:-10px; margin-top:0px; margin-right:-220px; max-width:390px; width:390px; height:70px; max-height:70px;">
                            {{$tracking->receiver->cust_address}}
                            {{$tracking->receiver->District->name_th}} 
                            {{$tracking->receiver->amphure->name_th}} 
                            {{$tracking->receiver->province->name_th}} 
                            {{$tracking->receiver->cust_postcode}}
                        </div>
                    </td>
                    <td width="15%" align='center'>
                        <span style='position: absolute; top:-10px; width:100%; font-size:16px;'>ลายเซ็นต์ผู้รับ</span>
                    </td>
                </tr>
            </table>
            {{-- <div align="right">
                <span style='font-size:20px;'>Tel: (02)007-4755 </span>
            </div> --}}
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
            <div style="line-height: 10px; position: absolute; bottom:0px; right:5px;">
                <span style='font-size:20px;'>Tel: (02)007-4755 </span>
            </div>
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