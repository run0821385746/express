@extends("welcome")
@section("content")

<div class="col-md-6">
    <div class="main-card mb-3 card">
        <div class="card-body">
            <h5 class="card-title">กำหนดสิทธิการเข้าถึงระบบ: {{ $permissionfor->emp_firstname.' '.$permissionfor->emp_lastname }}</h5><br>
            @if (!empty($permissionDetail->id))
                <form method="post" action="/permission/{{$permissionDetail->id}}">
                @method('PUT')  
            @else
             <form method="post" action="/permission">
            @endif
                {{csrf_field()}}
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="daily_summaries_menu" class="">สรุปยอดประจำวัน</label>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <fieldset class="position-relative form-group">
                                    <div class="position-relative form-check"><label class="form-check-label">
                                        <input name="daily_summaries_menu" type="radio" class="form-check-input"
                                                value="0" onChange="getDimensionInputView(this)" 
                                               @if ($permissionDetail->daily_summaries_menu == 0)
                                                   checked
                                               @endif
                                                > อนุญาต
                                        </label>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <fieldset class="position-relative form-group">
                                    <div class="position-relative form-check"><label class="form-check-label">
                                        <input name="daily_summaries_menu" type="radio" class="form-check-input"
                                            value="1" onChange="getDimensionInputView(this)"
                                            @if ($permissionDetail->daily_summaries_menu == 1)
                                                checked
                                            @endif
                                                > ไม่อนุญาต 
                                        </label>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="parcel_care_menu" class="">Parcel Care</label>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <fieldset class="position-relative form-group">
                                    <div class="position-relative form-check"><label class="form-check-label">
                                        <input name="parcel_care_menu" type="radio" class="form-check-input" value="0"
                                            onChange="getDimensionInputView(this)" 
                                            @if ($permissionDetail->parcel_care_menu == 0)
                                                checked
                                            @endif
                                                > อนุญาต
                                        </label>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <fieldset class="position-relative form-group">
                                    <div class="position-relative form-check"><label class="form-check-label">
                                        <input name="parcel_care_menu" type="radio" class="form-check-input" value="1"
                                            onChange="getDimensionInputView(this)"
                                            @if ($permissionDetail->parcel_care_menu == 1)
                                                checked
                                            @endif
                                                > ไม่อนุญาต 
                                            </label>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="receive_parcel_menu" class="">รับพัสดุใหม่</label>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <fieldset class="position-relative form-group">
                                    <div class="position-relative form-check"><label class="form-check-label">
                                        <input name="receive_parcel_menu" type="radio" class="form-check-input" value="0"
                                            onChange="getDimensionInputView(this)" 
                                            @if ($permissionDetail->receive_parcel_menu == 0)
                                                checked
                                            @endif
                                                > อนุญาต
                                        </label>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <fieldset class="position-relative form-group">
                                    <div class="position-relative form-check"><label class="form-check-label">
                                        <input name="receive_parcel_menu" type="radio" class="form-check-input" value="1"
                                            onChange="getDimensionInputView(this)"
                                            @if ($permissionDetail->receive_parcel_menu == 1)
                                                checked
                                            @endif
                                                > ไม่อนุญาต 
                                            </label>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="all_parcel_menu" class="">รายการพัสดุทั้งหมด</label>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <fieldset class="position-relative form-group">
                                    <div class="position-relative form-check"><label class="form-check-label">
                                        <input name="all_parcel_menu" type="radio" class="form-check-input"
                                            value="0" onChange="getDimensionInputView(this)" 
                                            @if ($permissionDetail->all_parcel_menu == 0)
                                                checked
                                            @endif
                                                > อนุญาต
                                        </label>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <fieldset class="position-relative form-group">
                                    <div class="position-relative form-check"><label class="form-check-label">
                                        <input name="all_parcel_menu" type="radio" class="form-check-input"
                                            value="1" onChange="getDimensionInputView(this)"
                                            @if ($permissionDetail->all_parcel_menu == 1)
                                                checked
                                            @endif
                                                > ไม่อนุญาต 
                                            </label>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="parcel_cls_menu" class="">พัสดุCLS</label>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <fieldset class="position-relative form-group">
                                    <div class="position-relative form-check"><label class="form-check-label">
                                        <input name="parcel_cls_menu" type="radio" class="form-check-input"
                                            value="0" onChange="getDimensionInputView(this)" 
                                            @if ($permissionDetail->parcel_cls_menu == 0)
                                                checked
                                            @endif
                                                > อนุญาต
                                        </label>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <fieldset class="position-relative form-group">
                                    <div class="position-relative form-check"><label class="form-check-label">
                                        <input name="parcel_cls_menu" type="radio" class="form-check-input"
                                            value="1" onChange="getDimensionInputView(this)"
                                            @if ($permissionDetail->parcel_cls_menu == 1)
                                                checked
                                            @endif
                                                > ไม่อนุญาต 
                                            </label>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="parcel_send_menu" class="">จ่ายพัสดุ</label>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <fieldset class="position-relative form-group">
                                    <div class="position-relative form-check"><label class="form-check-label">
                                        <input name="parcel_send_menu" type="radio" class="form-check-input"
                                            value="0" onChange="getDimensionInputView(this)" 
                                            @if ($permissionDetail->parcel_send_menu == 0)
                                                checked
                                            @endif
                                                > อนุญาต
                                        </label>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <fieldset class="position-relative form-group">
                                    <div class="position-relative form-check"><label class="form-check-label">
                                        <input name="parcel_send_menu" type="radio" class="form-check-input"
                                            value="1" onChange="getDimensionInputView(this)"
                                            @if ($permissionDetail->parcel_send_menu == 1)
                                                checked
                                            @endif
                                                > ไม่อนุญาต 
                                            </label>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="parcel_call_recive_menu" class="">เรียกรถเข้ารับพัสดุ</label>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <fieldset class="position-relative form-group">
                                    <div class="position-relative form-check"><label class="form-check-label">
                                        <input name="parcel_call_recive_menu" type="radio" class="form-check-input" value="0"
                                            onChange="getDimensionInputView(this)" 
                                            @if ($permissionDetail->parcel_call_recive_menu == 0)
                                                checked
                                            @endif
                                                > อนุญาต
                                        </label>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <fieldset class="position-relative form-group">
                                    <div class="position-relative form-check"><label class="form-check-label">
                                        <input name="parcel_call_recive_menu" type="radio" class="form-check-input" value="1"
                                            onChange="getDimensionInputView(this)"
                                            @if ($permissionDetail->parcel_call_recive_menu == 1)
                                                checked
                                            @endif
                                                > ไม่อนุญาต 
                                            </label>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="recive_parcel_from_dc_menu" class="">รับพัสดุจาก DC ต้นทาง</label>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <fieldset class="position-relative form-group">
                                    <div class="position-relative form-check"><label class="form-check-label">
                                        <input name="recive_parcel_from_dc_menu" type="radio" class="form-check-input" value="0"
                                            onChange="getDimensionInputView(this)" 
                                            @if ($permissionDetail->recive_parcel_from_dc_menu == 0)
                                                checked
                                            @endif
                                                > อนุญาต
                                        </label>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <fieldset class="position-relative form-group">
                                    <div class="position-relative form-check"><label class="form-check-label">
                                        <input name="recive_parcel_from_dc_menu" type="radio" class="form-check-input" value="1"
                                            onChange="getDimensionInputView(this)"
                                            @if ($permissionDetail->recive_parcel_from_dc_menu == 1)
                                                checked
                                            @endif
                                                > ไม่อนุญาต 
                                            </label>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="orther_report_menu" class="">รายงานต่างๆ</label>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <fieldset class="position-relative form-group">
                                    <div class="position-relative form-check"><label class="form-check-label">
                                        <input name="orther_report_menu" type="radio" class="form-check-input" value="0"
                                            onChange="getDimensionInputView(this)"
                                            @if ($permissionDetail->orther_report_menu == 0)
                                                checked
                                            @endif
                                                > อนุญาต
                                        </label>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <fieldset class="position-relative form-group">
                                    <div class="position-relative form-check"><label class="form-check-label">
                                        <input name="orther_report_menu" type="radio" class="form-check-input" value="1"
                                            onChange="getDimensionInputView(this)"
                                            @if ($permissionDetail->orther_report_menu == 1)
                                                checked
                                            @endif
                                                > ไม่อนุญาต 
                                            </label>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="customer_menu" class="">ข้อมูลลูกค้า</label>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <fieldset class="position-relative form-group">
                                    <div class="position-relative form-check"><label class="form-check-label">
                                        <input name="customer_menu" type="radio" class="form-check-input" value="0"
                                            onChange="getDimensionInputView(this)"
                                            @if ($permissionDetail->customer_menu == 0)
                                                checked
                                            @endif
                                                > อนุญาต
                                        </label>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <fieldset class="position-relative form-group">
                                    <div class="position-relative form-check"><label class="form-check-label">
                                        <input name="customer_menu" type="radio" class="form-check-input" value="1"
                                            onChange="getDimensionInputView(this)"
                                            @if ($permissionDetail->customer_menu == 1)
                                                checked
                                            @endif
                                                > ไม่อนุญาต 
                                            </label>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="employ_menu" class="">ข้อมูลพนักงาน</label>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <fieldset class="position-relative form-group">
                                    <div class="position-relative form-check"><label class="form-check-label">
                                        <input name="employ_menu" type="radio" class="form-check-input" value="0"
                                            onChange="getDimensionInputView(this)"
                                            @if ($permissionDetail->employ_menu == 0)
                                                checked
                                            @endif
                                                > อนุญาต
                                        </label>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <fieldset class="position-relative form-group">
                                    <div class="position-relative form-check"><label class="form-check-label">
                                        <input name="employ_menu" type="radio" class="form-check-input" value="1"
                                            onChange="getDimensionInputView(this)"
                                            @if ($permissionDetail->employ_menu == 1)
                                                checked
                                            @endif
                                                > ไม่อนุญาต 
                                            </label>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if ($employee->emp_position == 'เจ้าของกิจการ(Owner)')
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <div class="position-relative form-group">
                                <label for="permiss_menu" class="">กำหนดสิทธิ์การเข้าถึง</label>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="row">
                                <div class="col-lg-6 col-md-6">
                                    <fieldset class="position-relative form-group">
                                        <div class="position-relative form-check"><label class="form-check-label">
                                            <input name="permiss_menu" type="radio" class="form-check-input" value="0"
                                                onChange="getDimensionInputView(this)"
                                                @if ($permissionDetail->permiss_menu == 0)
                                                    checked
                                                @endif
                                                    > อนุญาต
                                            </label>
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <fieldset class="position-relative form-group">
                                        <div class="position-relative form-check"><label class="form-check-label">
                                            <input name="permiss_menu" type="radio" class="form-check-input" value="1"
                                                onChange="getDimensionInputView(this)"
                                                @if ($permissionDetail->permiss_menu == 1)
                                                    checked
                                                @endif
                                                    > ไม่อนุญาต 
                                                </label>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <div class="position-relative form-group">
                                <label for="dropcenter_menu" class="">ข้อมูล DropCenter</label>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="row">
                                <div class="col-lg-6 col-md-6">
                                    <fieldset class="position-relative form-group">
                                        <div class="position-relative form-check"><label class="form-check-label">
                                            <input name="dropcenter_menu" type="radio" class="form-check-input" value="0"
                                                onChange="getDimensionInputView(this)"
                                                @if ($permissionDetail->dropcenter_menu == 0)
                                                    checked
                                                @endif
                                                    > อนุญาต
                                            </label>
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <fieldset class="position-relative form-group">
                                        <div class="position-relative form-check"><label class="form-check-label">
                                            <input name="dropcenter_menu" type="radio" class="form-check-input" value="1"
                                                onChange="getDimensionInputView(this)"
                                                @if ($permissionDetail->dropcenter_menu == 1)
                                                    checked
                                                @endif
                                                    > ไม่อนุญาต 
                                                </label>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <div class="position-relative form-group">
                                <label for="orther_sale_menu" class="">ราคากล่องพัสดุ</label>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="row">
                                <div class="col-lg-6 col-md-6">
                                    <fieldset class="position-relative form-group">
                                        <div class="position-relative form-check"><label class="form-check-label">
                                            <input name="orther_sale_menu" type="radio" class="form-check-input" value="0"
                                                onChange="getDimensionInputView(this)"
                                                @if ($permissionDetail->orther_sale_menu == 0)
                                                    checked
                                                @endif
                                                    > อนุญาต
                                            </label>
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <fieldset class="position-relative form-group">
                                        <div class="position-relative form-check"><label class="form-check-label">
                                            <input name="orther_sale_menu" type="radio" class="form-check-input" value="1"
                                                onChange="getDimensionInputView(this)"
                                                @if ($permissionDetail->orther_sale_menu == 1)
                                                    checked
                                                @endif
                                                    > ไม่อนุญาต 
                                                </label>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <div class="position-relative form-group">
                                <label for="service_price_menu" class="">อัตราค่าบริการ</label>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="row">
                                <div class="col-lg-6 col-md-6">
                                    <fieldset class="position-relative form-group">
                                        <div class="position-relative form-check"><label class="form-check-label">
                                            <input name="service_price_menu" type="radio" class="form-check-input" value="0"
                                                onChange="getDimensionInputView(this)"
                                                @if ($permissionDetail->service_price_menu == 0)
                                                    checked
                                                @endif
                                                    > อนุญาต
                                            </label>
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <fieldset class="position-relative form-group">
                                        <div class="position-relative form-check"><label class="form-check-label">
                                            <input name="service_price_menu" type="radio" class="form-check-input" value="1"
                                                onChange="getDimensionInputView(this)"
                                                @if ($permissionDetail->service_price_menu == 1)
                                                    checked
                                                @endif
                                                    > ไม่อนุญาต 
                                                </label>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <div class="position-relative form-group">
                                <label for="parcel_type_menu" class="">ประเภทพัสดุและเงื่อนไข</label>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="row">
                                <div class="col-lg-6 col-md-6">
                                    <fieldset class="position-relative form-group">
                                        <div class="position-relative form-check"><label class="form-check-label">
                                            <input name="parcel_type_menu" type="radio" class="form-check-input" value="0"
                                                onChange="getDimensionInputView(this)"
                                                @if ($permissionDetail->parcel_type_menu == 0)
                                                    checked
                                                @endif
                                                    > อนุญาต
                                            </label>
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <fieldset class="position-relative form-group">
                                        <div class="position-relative form-check"><label class="form-check-label">
                                            <input name="parcel_type_menu" type="radio" class="form-check-input" value="1"
                                                onChange="getDimensionInputView(this)"
                                                @if ($permissionDetail->parcel_type_menu == 1)
                                                    checked
                                                @endif
                                                    > ไม่อนุญาต 
                                                </label>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if ($permissionDetail->id)
                    <div class="d-block text-center card-footer"><br>
                        <button class="btn-wide  btn btn-success">แก้ไขข้อมูล</button>
                    </div>
                @else
                    <div class="d-block text-center card-footer"><br>
                        <button class="btn-wide  btn btn-primary">บันทึกข้อมูล</button>
                    </div>
                @endif
                
            </form>
            <a href="/permission_get_list/{{$employee->emp_branch_id}}">
                <button class="btn-wide  btn btn-light" style="float: right;">กลับ</button>
            </a>
        </div>
    </div>

    @endsection