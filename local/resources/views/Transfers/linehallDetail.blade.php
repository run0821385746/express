
<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
            font-size: 16px;
        }
        
        p{
            line-height: 0.2;
        }
        th{
            line-height: 8px;
            padding-bottom: 10px;
            border:0px solid #ddd;
        }

        #table {
        font-family: "THSarabunNew";
        border-collapse: collapse;
        /* border:1px solid #ddd; */
        width: 100%;
        margin-left: -30px;
        margin-right: -20px;
        }

        .myDiv {   
        font-family: "THSarabunNew";
        font-size: 26px;
        text-align: center;
        }

        .myDCCODE {   
        font-family: "THSarabunNew";
        font-size: 26px;
        text-align: center;
        border-style: solid;
        }

        .page_break { page-break-before: always; }
    </style>
  

</head>
<body>
    @php
        $User = App\Model\User::where('employee_id',$TransferDropCenterBills->tranfer_employee_sender_id)->first();
        $Employee = App\Model\Employee::find($User->employee_id);
    @endphp
    <span style="position: absolute; right:0px; top:-30; color:rgb(82, 82, 82);">(For Drop Center)</span>
    <div align='center' style="font-weight: bold; font-size:20px;">Service Express(Thailand) Limited</div>
    <div align='center'>{{ $DropCentersender->drop_center_address.' '.$DropCentersender->District->name_th.' '.$DropCentersender->amphure->name_th.' '.$DropCentersender->province->name_th.' '.$DropCentersender->drop_center_postcode }}</div>
    <div>
        <div style="font-weight: bold; font-size:18px;">LINEHAUL MANNIFEST</div>
        <p>Batch ID: {{ $TransferDropCenterBills->transfer_bill_no }}</p>
        <p>Batch Date: {{ date_format($TransferDropCenterBills->created_at,"d/m/Y H:i:s") }}</p>
        <p>Sender name: {{ $Employee->emp_firstname.' '.$Employee->emp_lastname }}</p>
        <p>Truck ID: {{ $TransferDropCenterBills->tranfer_driver_sender_numberplate }}</p>
        <p>Driver Name: {{ $drivername->emp_firstname.' '.$drivername->emp_lastname.'('.$drivername->emp_phone.')' }}</p>
        <div style="position: absolute; right:0px; top:60px;" align='right'>
            <span style="font-weight: bold; font-size:18px;">For Shipping/Receiving</span>
            <br>
            <span>
                <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($TransferDropCenterBills->transfer_bill_no,'UPCE')}}" alt="barcode" style="margin-top:5px;" width="125px;" />
            </span>
            <br>
            <div style="margin-top:-15px; padding-right:5px; font-size:20px; font-weight: bold;">{{ $TransferDropCenterBills->transfer_bill_no }}</div>
        </div>
    </div>
    <br>
    <table style="width: 100%; font-size:16px; margin-top:-20px;" id="table">
        <tr style="background-color: #2e2e2e;  color:#fff;">
            <th width='8.79%'>DC</th>
            <th width='11.82%'>Consignmant No</th>
            <th width='10.35%'>Ref no</th>
            <th width='17.95%' style="border-left: 1px solid #fff;">Province</th>
            <th width='11.25%'>Desc<br>Post Code</th>
            <th width='9.69%'>DC Route</th>
            <th width='8.15%'>Totle Box</th>
            <th width='8.15%'>Box<br>on Truck</th>
            <th width='8.15%'>Act Weight</th>
        </tr>
        <tr>
            <td align="center" style="font-weight: bold;">{{ $DropCenterresive->drop_center_name_initial }}</td>
            <td></td>
            <td></td>
            <td colspan="6" align="right" style="font-weight: bold;">
                <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($TransferDropCenterBills->transfer_bill_no,'MSI')}}" alt="barcode" style="margin-top:5px;" />
                <br>
                <div style="margin-top:-15px; padding-right:5px; font-size:20px; font-weight: bold;">{{ $TransferDropCenterBills->transfer_bill_no }}-{{ $DropCentersender->drop_center_name_initial }}-{{ $DropCenterresive->drop_center_name_initial }}</div>
            </td>
        </tr>
        @php
            $trackingcountboxtotle = 0;
            $ActWeightTotle = 0;
        @endphp
        @foreach ($TransferDropCenters as $TransferDropCenter)
            @php
                $SubTracking = App\Model\SubTracking::where('subtracking_tracking_id',$TransferDropCenter->transfer_dropcenter_tracking_id)->get();
                $DimensionHistorys = App\Model\DimensionHistory::where('dimension_history_subtracking_id',$SubTracking[0]->id)->get();
                $province = App\Model\province::find($DropCentersender->drop_center_province);
                $ActWeight = 0;
                foreach ($DimensionHistorys as $DimensionHistory) {
                    $ActWeight += $DimensionHistory->dimension_history_weigth;
                }
                $ActWeight /= 1000;
                $ActWeightTotle += $ActWeight;
                $trackingcountbox = count($SubTracking);
                $trackingcountboxtotle += $trackingcountbox;
            @endphp
        <tr>
            <td></td>
            <td style="border-bottom:1px dotted #000;">{{ $TransferDropCenter->transfer_dropcenter_tracking_no }}</td>
            <td style="border-bottom:1px dotted #000;" align="center"><!--{{ $TransferDropCenter->transfer_bill_id_ref }}--></td>
            <td style="border-bottom:1px dotted #000;" align="center">{{ $province->name_th }}</td>
            <td style="border-bottom:1px dotted #000;" align="center">{{ $DropCentersender->drop_center_postcode }}</td>
            <td style="border-bottom:1px dotted #000;" align="center">{{ $DropCentersender->drop_center_name_initial }}</td>
            <td style="border-bottom:1px dotted #000;" align="right">{{ $trackingcountbox }}</td>
            <td style="border-bottom:1px dotted #000;" align="right">{{ $trackingcountbox }}</td>
            <td style="border-bottom:1px dotted #000;" align="right">{{ number_format($ActWeight,2) }}</td>
        </tr>
        @endforeach
        <tr>
            <td></td>
            <td style="border-bottom:1px solid #000; font-weight: bold;">Totle : {{ count($TransferDropCenters) }}</td>
            <td style="border-bottom:1px solid #000;" align="center"></td>
            <td style="border-bottom:1px solid #000;" align="center"></td>
            <td style="border-bottom:1px solid #000;" align="center"></td>
            <td style="border-bottom:1px solid #000;" align="center"></td>
            <td style="border-bottom:1px solid #000; font-weight: bold;" align="right">{{ $trackingcountboxtotle }}</td>
            <td style="border-bottom:1px solid #000; font-weight: bold;" align="right">{{ $trackingcountboxtotle }}</td>
            <td style="border-bottom:1px solid #000; font-weight: bold;" align="right">{{ number_format($ActWeightTotle,2) }}</td>
        </tr>
        <tr>
            <td></td>
            <td style="font-weight: bold; padding-right:5px;" colspan="4">Sander dc persons name ...........................................................................</td>
            <td style="font-weight: bold; padding-left:0px;" colspan="4"><span style="margin-left:-70px;">Driver persons name ..................................................................................</span></td>
        </tr>
        <tr>
            <td></td>
            <td style="font-weight: bold; padding-right:5px;" colspan="4">Receiving dc persons name .......................................................................</td>
            <td style="font-weight: bold; padding-left:0px;" colspan="4"><span style="margin-left:-70px;">Betch update time .....................................................................................</span></td>
        </tr>
        <tr>
            <td></td>
            <td style="font-weight: bold; padding-right:5px;" colspan="8">
                <table width="100%">
                    <tr>
                        <td width="15%">
                            Any discrepencies
                        </td>
                        <td width="85%">
                            <div style="border-bottom:1.5px dotted #000; margin-top:9px;"> </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="border-bottom:1px solid #000; font-weight: bold;" colspan="2">Grand Totle : {{ count($TransferDropCenters) }}</td>
            <td style="border-bottom:1px solid #000;" align="center"></td>
            <td style="border-bottom:1px solid #000;" align="center"></td>
            <td style="border-bottom:1px solid #000;" align="center"></td>
            <td style="border-bottom:1px solid #000;" align="center"></td>
            <td style="border-bottom:1px solid #000;" align="right">{{ $trackingcountboxtotle }}</td>
            <td style="border-bottom:1px solid #000;" align="right">{{ $trackingcountboxtotle }}</td>
            <td style="border-bottom:1px solid #000;" align="right">{{ number_format($ActWeightTotle,2) }}</td>
        </tr>
    </table>

    <div class="page_break">
        @php
            $User = App\Model\User::where('employee_id',$TransferDropCenterBills->tranfer_employee_sender_id)->first();
            $Employee = App\Model\Employee::find($User->employee_id);
        @endphp
        <span style="position: absolute; right:0px; top:-30; color:rgb(82, 82, 82);">(For Line Haul)</span>
        <div align='center' style="font-weight: bold; font-size:20px;">Service Express(Thailand) Limited</div>
        <div align='center'>{{ $DropCentersender->drop_center_address.' '.$DropCentersender->drop_center_sub_district.' '.$DropCentersender->drop_center_district.' '.$DropCentersender->drop_center_province.' '.$DropCentersender->drop_center_postcode }}</div>
        <div>
            <div style="font-weight: bold; font-size:18px;">LINEHAUL MANNIFEST</div>
            <p>Batch ID: {{ $TransferDropCenterBills->transfer_bill_no }}</p>
            <p>Batch Date: {{ date_format($TransferDropCenterBills->created_at,"d/m/Y H:i:s") }}</p>
            <p>Sender name: {{ $Employee->emp_firstname.' '.$Employee->emp_lastname }}</p>
            <p>Truck ID: {{ $TransferDropCenterBills->tranfer_driver_sender_numberplate }}</p>
            <p>Driver Name: {{ $drivername->emp_firstname.' '.$drivername->emp_lastname.'('.$drivername->emp_phone.')' }}</p>
            <div style="position: absolute; right:0px; top:60px;" align='right'>
                <span style="font-weight: bold; font-size:18px;">For Shipping/Receiving</span>
                <br>
                <span>
                    <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($TransferDropCenterBills->transfer_bill_no,'UPCE')}}" alt="barcode" style="margin-top:5px;" width="125px;" />
                </span>
                <br>
                <div style="margin-top:-15px; padding-right:5px; font-size:20px; font-weight: bold;">{{ $TransferDropCenterBills->transfer_bill_no }}</div>
            </div>
        </div>
        <br>
        <table style="width: 100%; font-size:16px; margin-top:-20px;" id="table">
            <tr style="background-color: #2e2e2e;  color:#fff;">
                <th width='8.79%'>DC</th>
                <th width='11.82%'>Consignmant No</th>
                <th width='10.35%'>Ref no</th>
                <th width='17.95%' style="border-left: 1px solid #fff;">Province</th>
                <th width='11.25%'>Desc<br>Post Code</th>
                <th width='9.69%'>DC Route</th>
                <th width='8.15%'>Totle Box</th>
                <th width='8.15%'>Box<br>on Truck</th>
                <th width='8.15%'>Act Weight</th>
            </tr>
            <tr>
                <td align="center" style="font-weight: bold;">{{ $DropCenterresive->drop_center_name_initial }}</td>
                <td></td>
                <td></td>
                <td colspan="6" align="right" style="font-weight: bold;">
                    <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($TransferDropCenterBills->transfer_bill_no,'MSI')}}" alt="barcode" style="margin-top:5px;" />
                    <br>
                    <div style="margin-top:-15px; padding-right:5px; font-size:20px; font-weight: bold;">{{ $TransferDropCenterBills->transfer_bill_no }}-{{ $DropCentersender->drop_center_name_initial }}-{{ $DropCenterresive->drop_center_name_initial }}</div>
                </td>
            </tr>
            @php
                $trackingcountboxtotle = 0;
                $ActWeightTotle = 0;
            @endphp
            @foreach ($TransferDropCenters as $TransferDropCenter)
                @php
                    $SubTracking = App\Model\SubTracking::where('subtracking_tracking_id',$TransferDropCenter->transfer_dropcenter_tracking_id)->get();
                    $DimensionHistorys = App\Model\DimensionHistory::where('dimension_history_subtracking_id',$SubTracking[0]->id)->get();
                    $province = App\Model\province::find($DropCentersender->drop_center_province);
                    $ActWeight = 0;
                    foreach ($DimensionHistorys as $DimensionHistory) {
                        $ActWeight += $DimensionHistory->dimension_history_weigth;
                    }
                    $ActWeight /= 1000;
                    $ActWeightTotle += $ActWeight;
                    $trackingcountbox = count($SubTracking);
                    $trackingcountboxtotle += $trackingcountbox;
                @endphp
            <tr>
                <td></td>
                <td style="border-bottom:1px dotted #000;">{{ $TransferDropCenter->transfer_dropcenter_tracking_no }}</td>
                <td style="border-bottom:1px dotted #000;" align="center"><!--{{ $TransferDropCenter->transfer_bill_id_ref }}--></td>
                <td style="border-bottom:1px dotted #000;" align="center">{{ $province->name_th }}</td>
                <td style="border-bottom:1px dotted #000;" align="center">{{ $DropCentersender->drop_center_postcode }}</td>
                <td style="border-bottom:1px dotted #000;" align="center">{{ $DropCentersender->drop_center_name_initial }}</td>
                <td style="border-bottom:1px dotted #000;" align="right">{{ $trackingcountbox }}</td>
                <td style="border-bottom:1px dotted #000;" align="right">{{ $trackingcountbox }}</td>
                <td style="border-bottom:1px dotted #000;" align="right">{{ number_format($ActWeight,2) }}</td>
            </tr>
            @endforeach
            <tr>
                <td></td>
                <td style="border-bottom:1px solid #000; font-weight: bold;">Totle : {{ count($TransferDropCenters) }}</td>
                <td style="border-bottom:1px solid #000;" align="center"></td>
                <td style="border-bottom:1px solid #000;" align="center"></td>
                <td style="border-bottom:1px solid #000;" align="center"></td>
                <td style="border-bottom:1px solid #000;" align="center"></td>
                <td style="border-bottom:1px solid #000; font-weight: bold;" align="right">{{ $trackingcountboxtotle }}</td>
                <td style="border-bottom:1px solid #000; font-weight: bold;" align="right">{{ $trackingcountboxtotle }}</td>
                <td style="border-bottom:1px solid #000; font-weight: bold;" align="right">{{ number_format($ActWeightTotle,2) }}</td>
            </tr>
            <tr>
                <td></td>
                <td style="font-weight: bold; padding-right:5px;" colspan="4">Sander dc persons name ...........................................................................</td>
                <td style="font-weight: bold; padding-left:0px;" colspan="4"><span style="margin-left:-70px;">Driver persons name ..................................................................................</span></td>
            </tr>
            <tr>
                <td></td>
                <td style="font-weight: bold; padding-right:5px;" colspan="4">Receiving dc persons name .......................................................................</td>
                <td style="font-weight: bold; padding-left:0px;" colspan="4"><span style="margin-left:-70px;">Betch update time .....................................................................................</span></td>
            </tr>
            <tr>
                <td></td>
                <td style="font-weight: bold; padding-right:5px;" colspan="8">
                    <table width="100%">
                        <tr>
                            <td width="15%">
                                Any discrepencies
                            </td>
                            <td width="85%">
                                <div style="border-bottom:1.5px dotted #000; margin-top:9px;"> </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="border-bottom:1px solid #000; font-weight: bold;" colspan="2">Grand Totle : {{ count($TransferDropCenters) }}</td>
                <td style="border-bottom:1px solid #000;" align="center"></td>
                <td style="border-bottom:1px solid #000;" align="center"></td>
                <td style="border-bottom:1px solid #000;" align="center"></td>
                <td style="border-bottom:1px solid #000;" align="center"></td>
                <td style="border-bottom:1px solid #000;" align="right">{{ $trackingcountboxtotle }}</td>
                <td style="border-bottom:1px solid #000;" align="right">{{ $trackingcountboxtotle }}</td>
                <td style="border-bottom:1px solid #000;" align="right">{{ number_format($ActWeightTotle,2) }}</td>
            </tr>
        </table>
        {{-- <table style="width: 100%; font-size:16px; margin-top:-20px;" id="table">
            <tr style="background-color: #2e2e2e;  color:#fff;">
                <th width='8.79%'>DC</th>
                <th width='11.82%'>Consignmant No</th>
                <th width='10.35%'>Ref no</th>
                <th width='7.70%' style="border-left: 1px solid #fff;">Cage No</th>
                <th width='10.25%'>Province</th>
                <th width='9.25%'>Desc<br>Post Code</th>
                <th width='7.69%'>DC Route</th>
                <th width='6.15%'>Totle Box</th>
                <th width='6.15%'>Box<br>on Truck</th>
                <th width='6.15%'>Act Weight</th>
                <th width='5%'>CBM</th>
                <th width='5%'>Service Type</th>
            </tr>
            <tr>
                <td align="center" style="font-weight: bold;">{{ $DropCenterresive->drop_center_name_initial }}</td>
                <td></td>
                <td></td>
                <td colspan="9" align="right" style="font-weight: bold;">
                    <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($TransferDropCenterBills->transfer_bill_no,'MSI')}}" alt="barcode" style="margin-top:5px;" />
                    <br>
                    <div style="margin-top:-15px; padding-right:5px; font-size:20px; font-weight: bold;">{{ $TransferDropCenterBills->transfer_bill_no }}-{{ $DropCentersender->drop_center_name_initial }}-{{ $DropCenterresive->drop_center_name_initial }}</div>
                </td>
            </tr>
            @php
                $trackingcountboxtotle = 0;
                $ActWeightTotle = 0;
            @endphp
            @foreach ($TransferDropCenters as $TransferDropCenter)
                @php
                    $SubTracking = App\Model\SubTracking::where('subtracking_tracking_id',$TransferDropCenter->transfer_dropcenter_tracking_id)->get();
                    $DimensionHistorys = App\Model\DimensionHistory::where('dimension_history_subtracking_id',$SubTracking[0]->id)->get();
                    $ActWeight = 0;
                    foreach ($DimensionHistorys as $DimensionHistory) {
                        $ActWeight += $DimensionHistory->dimension_history_weigth;
                    }
                    $ActWeight /= 1000;
                    $ActWeightTotle += $ActWeight;
                    $trackingcountbox = count($SubTracking);
                    $trackingcountboxtotle += $trackingcountbox;
                @endphp
            <tr>
                <td></td>
                <td style="border-bottom:1px dotted #000;">{{ $TransferDropCenter->transfer_dropcenter_tracking_no }}</td>
                <td style="border-bottom:1px dotted #000;" align="center">{{ $TransferDropCenter->transfer_bill_id_ref }}</td>
                <td style="border-bottom:1px dotted #000;" align="center"></td>
                <td style="border-bottom:1px dotted #000;" align="center">{{ $DropCentersender->drop_center_province }}</td>
                <td style="border-bottom:1px dotted #000;" align="center">{{ $DropCentersender->drop_center_postcode }}</td>
                <td style="border-bottom:1px dotted #000;" align="center">{{ $DropCentersender->drop_center_name_initial }}</td>
                <td style="border-bottom:1px dotted #000;" align="right">{{ $trackingcountbox }}</td>
                <td style="border-bottom:1px dotted #000;" align="right">{{ $trackingcountbox }}</td>
                <td style="border-bottom:1px dotted #000;" align="right">{{ number_format($ActWeight,2) }}</td>
                <td style="border-bottom:1px dotted #000;" align="right">{{ number_format(0,2) }}</td>
                <td style="border-bottom:1px dotted #000;" align="right">ND</td>
            </tr>
            @endforeach
            <tr>
                <td></td>
                <td style="border-bottom:1px solid #000; font-weight: bold;">Totle : {{ count($TransferDropCenters) }}</td>
                <td style="border-bottom:1px solid #000;" align="center"></td>
                <td style="border-bottom:1px solid #000;" align="center"></td>
                <td style="border-bottom:1px solid #000;" align="center"></td>
                <td style="border-bottom:1px solid #000;" align="center"></td>
                <td style="border-bottom:1px solid #000;" align="center"></td>
                <td style="border-bottom:1px solid #000; font-weight: bold;" align="right">{{ $trackingcountboxtotle }}</td>
                <td style="border-bottom:1px solid #000; font-weight: bold;" align="right">{{ $trackingcountboxtotle }}</td>
                <td style="border-bottom:1px solid #000; font-weight: bold;" align="right">{{ number_format($ActWeightTotle,2) }}</td>
                <td style="border-bottom:1px solid #000; font-weight: bold;" align="right">0.00</td>
                <td style="border-bottom:1px solid #000;" align="right"></td>
            </tr>
            <tr>
                <td></td>
                <td style="font-weight: bold; padding-right:5px;" colspan="4">Sander dc persons name .................................................................</td>
                <td style="font-weight: bold; padding-left:0px; " colspan="7">Driver persons name ............................................................................................</td>
            </tr>
            <tr>
                <td></td>
                <td style="font-weight: bold; padding-right:5px;" colspan="4">Receiving dc persons name .............................................................</td>
                <td style="font-weight: bold; padding-left:0px;" colspan="7">Betch update time ................................................................................................</td>
            </tr>
            <tr>
                <td></td>
                <td style="font-weight: bold; padding-right:5px;" colspan="11">
                    <table width="100%">
                        <tr>
                            <td width="15%">
                                Any discrepencies
                            </td>
                            <td width="85%">
                                <div style="border-bottom:1.5px dotted #000; margin-top:9px;"> </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="border-bottom:1px solid #000; font-weight: bold;" colspan="2">Grand Totle : {{ count($TransferDropCenters) }}</td>
                <td style="border-bottom:1px solid #000;" align="center"></td>
                <td style="border-bottom:1px solid #000;" align="center"></td>
                <td style="border-bottom:1px solid #000;" align="center"></td>
                <td style="border-bottom:1px solid #000;" align="center"></td>
                <td style="border-bottom:1px solid #000;" align="center"></td>
                <td style="border-bottom:1px solid #000;" align="right">{{ $trackingcountboxtotle }}</td>
                <td style="border-bottom:1px solid #000;" align="right">{{ $trackingcountboxtotle }}</td>
                <td style="border-bottom:1px solid #000;" align="right">{{ number_format($ActWeightTotle,2) }}</td>
                <td style="border-bottom:1px solid #000;" align="right">0.00</td>
                <td style="border-bottom:1px solid #000;" align="right"></td>
            </tr>
        </table> --}}
    </div>

  
</body>
</html>
<script src="http://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript" src="barcode/jquery/jquery-barcode.js"></script>