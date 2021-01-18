@extends("welcome")
@section("content")

<div class="col-md-12" style="margin-right: -15px !important; margin-left: -15px !important;">
    <div class="main-card mb-6 card" style="margin-right: -25px !important;">
        <div class="card-header">
            <div id="title_header">
                กำหนดสิทธิ์การเข้าถีงระบบ
            </div>
        </div>
        <div class="card-body" id="forget-width">
            <div class="table-responsive" style="margin-left:-2.5px; margin-right:-2.5px; max-width:100%;">
                <div id="username_list_permission"></div>
                    
            </div>
        </div>
        {{-- <div class="d-block text-left card-footer">
            <div class="row">   
                <div class="col-lg-6 col-md-6"></div>
                <div class="col-lg-6 col-md-6">
                    <form action="{{url('print_report')}}" method="post" target="_blank">
                        {{csrf_field()}}
                        <input type="hidden" name="dropcenter_pdf" id="dropcenter_pdf">
                        <input type="hidden" name="report_type_pdf" id="report_type_pdf">
                        <input type="hidden" name="selectdateFrom_pdf" id="selectdateFrom_pdf">
                        <input type="hidden" name="selectdateTo_pdf" id="selectdateTo_pdf">
                        <button class="btn-wide btn btn-success pull-right" id="print_report_submit" type="submit" onclick="prunt_pdf()">PDF Print</button>
                    </form>
                </div>
            </div>
        </div> --}}
    </div>


<script>
    width = $("#forget-width").width();
    $(".table-responsive").css("max-width",width+"px")
    getlistname();
    function getlistname(){
        content = "<table class='table data-table'>";
            content += "<thead>";
                content += "<tr>";
                    content += "<th>NO</th>";
                    content += "<th>ชื่อ-สกุล</th>";
                    content += "<th>ตำแหน่งงาน</th>";
                    content += "<th>สรุปยอดประจำวัน</th>";
                    content += "<th>Parcel Care</th>";
                    content += "<th>รับพัสดุใหม่</th>";
                    content += "<th>รายการพัสดุทั้งหมด</th>";
                    content += "<th>พัสดุCLS</th>";
                    content += "<th>จ่ายพัสดุ</th>";
                    content += "<th>เรียกรถเข้ารับพัสดุ</th>";
                    content += "<th>รับพัสดุจาก DC ต้นทาง</th>";
                    content += "<th>รายงานต่างๆ</th>";
                    content += "<th>ข้อมูลลูกค้า</th>";
                    content += "<th>ข้อมูลพนักงาน</th>";
                    content += "<th>กำหนดสิทธิ์การเข้าถึง</th>";
                    content += "<th>ข้อมูล DropCenter</th>";
                    content += "<th>ราคากล่องพัสดุ</th>";
                    content += "<th>อัตราค่าบริการ</th>";
                    content += "<th>ประเภทพัสดุและเงื่อนไข</th>";
                    content += "<th>ทำรายการ</th>";
                content += "</tr>";
            content += "</thead>";
            content += "<tbody>";
            content += "</tbody>";
        content += "</table>";
        $("#username_list_permission").html(content);
        $(function () {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    method:"POST",
                    url:"{{url('permissionGetListDataTable')}}",
                    dataType: 'json',
                    data:{
                            "_token": "{{ csrf_token() }}"
                        },
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'emp_name', name: 'emp_name', className:"text-center"},
                    {data: 'emp_position', name: 'emp_position', className:"text-center"},
                    {data: 'daily_summaries_menu', name: 'daily_summaries_menu', className:"text-center"},
                    {data: 'parcel_care_menu', name: 'parcel_care_menu', className:"text-center"},
                    {data: 'receive_parcel_menu', name: 'receive_parcel_menu', className:"text-center"},
                    {data: 'all_parcel_menu', name: 'all_parcel_menu', className:"text-center"},
                    {data: 'parcel_cls_menu', name: 'parcel_cls_menu', className:"text-center"},
                    {data: 'parcel_send_menu', name: 'parcel_send_menu', className:"text-center"},
                    {data: 'parcel_call_recive_menu', name: 'parcel_call_recive_menu', className:"text-center"},
                    {data: 'recive_parcel_from_dc_menu', name: 'recive_parcel_from_dc_menu', className:"text-center"},
                    {data: 'orther_report_menu', name: 'orther_report_menu', className:"text-center"},
                    {data: 'customer_menu', name: 'customer_menu', className:"text-center"},
                    {data: 'employ_menu', name: 'employ_menu', className:"text-center"},
                    {data: 'permiss_menu', name: 'permiss_menu', className:"text-center"},
                    {data: 'dropcenter_menu', name: 'dropcenter_menu', className:"text-center"},
                    {data: 'orther_sale_menu', name: 'orther_sale_menu', className:"text-center"},
                    {data: 'service_price_menu', name: 'service_price_menu', className:"text-center"},
                    {data: 'parcel_type_menu', name: 'parcel_type_menu', className:"text-center"},
                    {data: 'action', name: 'action', className:"text-center", orderable: false, searchable: false}
                ]
            });
        });
    }
</script>
@endsection