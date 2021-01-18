
<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page { 
            margin: 15px;
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
            page-break-inside: avoid;
            font-size:27px;
            line-height:30px;
        }
    </style>

</head>
<body>
        @php
            $droupsentername = str_replace("SERVICE EXPRESS ", "", $dropCenter->drop_center_name);
        @endphp
    <div align='center' style='margin-top:20px;'>
        <div style='font-size:35px; line-height:30px; font-weight: bold;'>SERVICE EXPRESS</div>
        <div style='font-size:35px; line-height:30px; margin-top:-5px; font-weight: bold;'>{{$droupsentername}}</div>
        <div style='font-size:35px; line-height:30px; margin-top:-5px; font-weight: bold;'>Tel/Fax: {{$dropCenter->drop_center_phone}}</div>
        <div style='font-size:35px; line-height:30px; margin-top:-5px; font-weight: bold;'>*..ขอบคุณที่มาใช้บริการ..*</div>
    </div>
    <div>
        <div style='font-size:35px; line-height:30px;'>วันที่ : {{$booking->created_at}}</div>
        <div style='font-size:35px; margin-top:-5px; line-height:30px;'>บิลเลขที่ : {{$booking->booking_no}}</div>
        <div style='font-size:35px; margin-top:-5px; line-height:30px;'>แคทเชียร์ : {{$employee->emp_firstname.' '.$employee->emp_lastname}}</div>
    </div>
    <hr style="border:0.4px solid #000;">
    <table width='100%'>
        <tr>
            <th width='60%'></th>
            <th width='30%' align='right'></th>
            <th width='10%'></th>
        </tr>
        @php
            $i = 0;
        @endphp
        @foreach ($trackings as $tracking)
            @php
                $i++;
                $subTrackings = App\Model\SubTracking::where('subtracking_tracking_id',$tracking->id)->get();
                $SaleOthers = App\Model\SaleOther::where('sale_other_tr_id',$tracking->id)->get();
                $amphure = App\Model\amphure::find($tracking->cust_district);
            @endphp
            
            <?php
                if($i == '1'){
            ?>
                <tr>
                    <td colspan="3">{{$i.'. '.$amphure->name_th.' '.$tracking->cust_postcode}}</td>
                </tr>
                <tr style='paddong:0px; line-height:20px;'>
                    <td colspan="3">{{'เลขอ้างอิง : '.$tracking->tracking_no}}</td>
                </tr>
                @if ($customer_sender->cust_cod_register_status != null)
                    <tr style='paddong:0px; line-height:20px;'>
                        <td colspan="3">บัญชี COD <strong>{{$customer_sender->cust_cod_register_status}}</strong></td>
                    </tr>
                @endif
                <?php
                    $isubtrack = 0;
                ?>
                @foreach ($subTrackings as $subTracking)
                    @php
                        $DimensionHistorys = App\Model\DimensionHistory::where('dimension_history_subtracking_id',$subTracking->id)->get();
                        $isubtrack++;
                    @endphp
                    @if ($isubtrack != '1')
                        <tr style='paddong:0px; line-height:20px;'>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    @endif
                    <tr style='paddong:0px; line-height:20px;'>
                        <td>- น้ำหนัก</td>
                        <td align='right'>
                            @foreach ($DimensionHistorys as $DimensionHistory)
                                {{$DimensionHistory->dimension_history_weigth}}
                            @endforeach
                        </td>
                        <td>กรัม</td>
                    </tr>
                    @if($subTracking->subtracking_cod_fee != '0')
                        <tr style='paddong:0px; line-height:20px;'>
                            <td>- ยอดรวม COD</td>
                            <td align='right'>
                                {{number_format($subTracking->subtracking_cod,2)}}
                            </td>
                            <td></td>
                        </tr>
                    @endif
                    <tr style='paddong:0px; line-height:20px;'>
                        <td>- ค่าธรรมเนียม</td>
                        <td align='right'>{{number_format($subTracking->subtracking_price,2)}}</td>
                        <td></td>
                    </tr>
                    @if($subTracking->subtracking_cod_fee != '0')
                        <tr style='paddong:0px; line-height:20px;'>
                            <td>- ค่าธรรมเนียม COD</td>
                            <td align='right'>{{number_format($subTracking->subtracking_cod_fee,2)}}</td>
                            <td></td>
                        </tr>
                    @endif
                    <tr style='paddong:0px; line-height:20px;'>
                        <td>- ค่าบริการ</td>
                        <td align='right'>{{number_format(0,2)}}</td>
                        <td></td>
                    </tr>
                @endforeach
                @if (count($SaleOthers) > 0)
                    <tr style='paddong:0px; line-height:20px;'>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                @endif
                @foreach ($SaleOthers as $SaleOther)
                    <tr style='paddong:0px; line-height:20px;'>
                        <td>- {{$SaleOther->productPrice->product_name}}</td>
                        <td align='right'>{{number_format($SaleOther->sale_other_price,2)}}</td>
                        <td></td>
                    </tr>
                @endforeach
                <tr style='paddong:0px; line-height:20px;'>
                    <td>รวมเป็นเงิน</td>
                    <td align='right' style='border-bottom: 0.09px solid rgb(0, 0, 0); border-top: 0.09px solid rgb(0, 0, 0);'>{{ number_format($tracking->tracking_amount,2) }}</td>
                    <td></td>
                </tr>
                <tr style='paddong:0px; line-height:20px;'>
                    <td colspan="3">{{'ผู้รับ '.$tracking->cust_name}}</td>
                </tr>
            <?php
                }else{
            ?>
                <tr>
                    <td colspan="3">
                        <hr style="border: 0.09px solid rgb(0, 0, 0); border-style: solid;">
                        {{$i.'. '.$amphure->name_th.' '.$tracking->cust_postcode}}
                    </td>
                </tr>
                <tr style='paddong:0px; line-height:20px;'>
                    <td colspan="3">{{'เลขอ้างอิง : '.$tracking->tracking_no}}</td>
                </tr>
                <?php
                    $isubtrack = 0;
                ?>
                @foreach ($subTrackings as $subTracking)
                    @php
                        $DimensionHistorys = App\Model\DimensionHistory::where('dimension_history_subtracking_id',$subTracking->id)->get();
                        $isubtrack++;
                    @endphp
                    @if ($isubtrack != '1')
                        <tr style='paddong:0px; line-height:20px;'>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    @endif
                    <tr style='paddong:0px; line-height:20px;'>
                        <td>- น้ำหนัก</td>
                        <td align='right'>
                            @foreach ($DimensionHistorys as $DimensionHistory)
                                {{$DimensionHistory->dimension_history_weigth}}
                            @endforeach
                        </td>
                        <td>กรัม</td>
                    </tr>
                    @if($subTracking->subtracking_cod_fee != '0')
                        <tr style='paddong:0px; line-height:20px;'>
                            <td>- ยอดรวม COD</td>
                            <td align='right'>
                                {{number_format($subTracking->subtracking_cod,2)}}
                            </td>
                            <td></td>
                        </tr>
                    @endif
                    <tr style='paddong:0px; line-height:20px;'>
                        <td>- ค่าธรรมเนียม</td>
                        <td align='right'>{{number_format($subTracking->subtracking_price,2)}}</td>
                        <td></td>
                    </tr>
                    @if($subTracking->subtracking_cod_fee != '0')
                        <tr style='paddong:0px; line-height:20px;'>
                            <td>- ค่าธรรมเนียม COD</td>
                            <td align='right'>{{number_format($subTracking->subtracking_cod_fee,2)}}</td>
                            <td></td>
                        </tr>
                    @endif
                    <tr style='paddong:0px; line-height:20px;'>
                        <td>- ค่าบริการ</td>
                        <td align='right'>{{number_format(0,2)}}</td>
                        <td></td>
                    </tr>
                @endforeach
                @if (count($SaleOthers) > 0)
                    <tr style='paddong:0px; line-height:20px;'>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                @endif
                @foreach ($SaleOthers as $SaleOther)
                    <tr style='paddong:0px; line-height:20px;'>
                        <td>- {{$SaleOther->productPrice->product_name}}</td>
                        <td align='right'>{{number_format($SaleOther->sale_other_price,2)}}</td>
                        <td></td>
                    </tr>
                @endforeach
                <tr style='paddong:0px; line-height:20px;'>
                    <td>รวมเป็นเงิน</td>
                    <td align='right' style='border-bottom: 0.09px solid rgb(0, 0, 0); border-top: 0.09px solid rgb(0, 0, 0);'>{{ number_format($tracking->tracking_amount,2) }}</td>
                    <td></td>
                </tr>
                <tr style='paddong:0px; line-height:20px;'>
                    <td colspan="3">{{'ผู้รับ '.$tracking->cust_name}}</td>
                </tr>


                {{-- <tr>
                    <td colspan="3">
                        <hr style="border: 0.09px solid rgb(0, 0, 0); border-style: solid;">
                        {{$i.'. '.$tracking->cust_district.' '.$tracking->cust_postcode}}
                    </td>
                </tr>
                <tr style='paddong:0px; line-height:20px;'>
                    <td colspan="3">{{'เลขอ้างอิง : '.$tracking->tracking_no}}</td>
                </tr>
                @foreach ($subTrackings as $subTracking)
                    @php
                        $DimensionHistorys = App\Model\DimensionHistory::where('dimension_history_subtracking_id',$subTracking->id)->get();
                        // dd($DimensionHistorys);
                    @endphp
                    <tr style='paddong:0px; line-height:20px;'>
                        <td>- น้ำหนัก</td>
                        <td>
                            @foreach ($DimensionHistorys as $DimensionHistory)
                                {{$DimensionHistory->dimension_history_weigth}}
                            @endforeach
                            กรัม
                        </td>
                        <td></td>
                    </tr>
                    <tr style='paddong:0px; line-height:20px;'>
                        <td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ค่าธรรมเนียม</td>
                        <td align='right'>{{number_format($subTracking->subtracking_price,2)}}</td>
                        <td>บาท</td>
                    </tr>
                @endforeach
                @foreach ($SaleOthers as $SaleOther)
                    <tr style='paddong:0px; line-height:20px;'>
                        <td>- {{$SaleOther->productPrice->product_name}}</td>
                        <td align='right'>{{number_format($SaleOther->sale_other_price,2)}}</td>
                        <td>บาท</td>
                    </tr>
                @endforeach
                <tr style='paddong:0px; line-height:20px;'>
                    <td>รวมเป็นเงิน</td>
                    <td align='right' style='border-bottom: 0.09px solid rgb(0, 0, 0); border-top: 0.09px solid rgb(0, 0, 0);'>{{ number_format($tracking->tracking_amount,2) }}</td>
                    <td>บาท</td>
                </tr>
                <tr style='paddong:0px; line-height:20px;'>
                    <td colspan="3">{{'ผู้รับ '.$tracking->cust_name}}</td>
                </tr> --}}
            <?php
                }
            ?>
        @endforeach
    </table>
    <hr style="border:0.4px solid #000;">
    <table width='100%'>
        <tr>
            <th width='70%'></th>
            <th width='30%'></th>
        </tr>
        <tr>
            <td>ยอดรวม</td>
            <td align='right'><strong style="font-size: 24px;">{{ number_format($booking->booking_amount,2) }}</strong></td>
        </tr>
        <tr style='paddong:0px; line-height:20px;'>
            <td style='paddong:0px; paddong:0px;'>ยอดรับ</td>
            <td align='right' style="border-top: 0.3px solid rgb(0, 0, 0); paddong:0px;">{{ number_format($recive,2) }}</td>
        </tr>
        <tr style='paddong:0px; line-height:14px;'>
            <td style='paddong:0px; paddong:0px;'>เงินทอน</td>
            <td align='right' style="border-bottom: 0.3px solid rgb(0, 0, 0); paddong:0px;">{{ number_format($slipchange,2) }}</td>
        </tr>
    </table>
    <div align='center' style='margin-top:0px;'>
        <div style='font-size:35px; margin-top:0px; line-height:30px;'>พบสิ่งของต้องห้ามผิดกฎหมาย</div>
        <div style='font-size:35px; margin-top:-5px; line-height:30px;'>ทางร้านไม่ขอรับผิดชอบใดๆทั้งสิ้น</div>
        <div style='font-size:35px; margin-top:-5px; line-height:30px;'>สามารถเช็กพัสดุที่ท่านจัดส่งได้ที่</div>
        <div style='font-size:35px; margin-top:-5px; line-height:30px;'>www.service-express.com/check-tracking</div>
    </div>
    <br>
    {{-- <div class="col-md-4">
        <div class="main-card mb-3 card">
            <div class="row">
                <div class="col-lg-12">
                    <table class="align-middle mb-0 table table-borderless ">
                        <tbody>
                            <tr>
                                <td class="text-center">
                                   <h5>{{$dropCenter->drop_center_name}}</h5>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">
                                    ที่อยู่ {{$dropCenter->drop_center_address}}
                                    ตำบล {{$dropCenter->drop_center_sub_district}}
                                    อำเภอ {{$dropCenter->drop_center_district}}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">
                                    จังหวัด {{$dropCenter->drop_center_province}}
                                    รหัสไปรษณีย์ {{$dropCenter->drop_center_postcode}}
                                    โทร {{$dropCenter->drop_center_phone}}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">
                                    <h5>ใบเสร็จรับเงิน</h5>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-left"> วันที่ขาย : {{$booking->created_at}} </td>
                            </tr>
                            <tr>
                                <td class="text-left"> บิลเลขที่ : {{$booking->booking_no}} </td>
                            </tr>
                            <tr>
                                <td class="text-left"> พนักงานขาย : {{$dropCenter->drop_center_address}} </td>
                            </tr>
                          
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <table class="align-middle mb-0 table table-borderless ">
                        <tbody>
                            @php
                                $i = 1;
                                $trackBillAmount = 0;
                            @endphp
                           @foreach ($trackings as $tracking)
                             <tr>
                                <td width="70" class="text-left"></td>
                                <td width="30" class="text-left"></td>
                             </tr>
                            <tr>
                                <td colspan="2" class="text-left"> 
                                    {{$i++}} {{$tracking->receiver->cust_province}} {{$tracking->receiver->cust_postcode}}
                                </td>
                            </tr>
                                @php
                                    $subTrackings = App\Model\SubTracking::where('subtracking_tracking_id',$tracking->id)->get();
                                    $totalBillAmount = 0;
                                @endphp
                                @foreach ($subTrackings as $subTracking)
                                <tr>
                                    <td  class="text-left"> __{{$subTracking->parceltype->parcel_type_name}}</td>
                                    <td  class="text-right"> 
                                         {{number_format($subTracking->subtracking_price,2)}}
                                         @php
                                             $totalBillAmount += $subTracking->subtracking_price;
                                         @endphp
                                    </td>
                                </tr>
                                @endforeach
                            @php
                                $trackBillAmount += $totalBillAmount;
                            @endphp
                            <tr>
                                <td colspan="2" class="text-left">
                                     ผู้รับ: {{$tracking->receiver->cust_name}} </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-left"> Tracking: {{$tracking->tracking_no}} </td>
                            </tr>
                           @endforeach
                        </tbody>
                    </table>
                </div>
            </div> 
            <div class="row">
                <div class="col-lg-12">
                    <table class="align-middle mb-0 table table-borderless ">
                     
                        <tbody>
    
                            <tr><td colspan="2" class="text-center"></td></tr>
                            <tr>
                                <td class="text-left"><h6>รวมเป็นเงิน</h6></td>
                                <td class="text-right">
                                    <h6>{{number_format($trackBillAmount,2)}}
                                    </h6>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-left"><h6>รับเงิน</h6></td>
                                <td class="text-right"><h6>
                                    {{number_format($trackBillAmount,2)}}
                                </h6>
                            </td>
                            </tr>
                            <tr>
                                <td class="text-left"><h6>เงินทอน</h6></td>
                                <td class="text-right"><h6>
                                    {{number_format($trackBillAmount,2)}}
                                </h6>
                            </td>
                            </tr>
                            <tr><td colspan="2" class="text-center"></td></tr>
                            <tr><td colspan="2" class="text-center"></td></tr>
                            <tr><td colspan="2" class="text-center">หากพบสิ่งต้องห้ามผิดกฎหมาย</td></tr>
                            <tr><td colspan="2" class="text-center">ทางร้านไม่ขอรับผิดชอบใดๆทั้งสิ้น</td></tr>
                            <tr><td colspan="2" class="text-center">สามารถเช็คสถานะพัสดุที่ส่งได้ที่</td></tr>
                            <tr><td colspan="2" class="text-center">www.service-express.co.th/tracking.php</td></tr>
                            <tr><td colspan="2" class="text-center"></td></tr>
                            <tr><td colspan="2" class="text-center"><h5>ขอบคุณที่ใช้บริการ</h5></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> --}}
  
</body>
</html>
