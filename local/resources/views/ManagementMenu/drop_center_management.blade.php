@extends("welcome")
@section("content")

<div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="card-header"> DropCenter List
        </div>
        <div class="card-body table-responsive">
            <table class="data-table align-middle mb-0 table table-borderless table-striped table-hover">
                <thead>
                    <tr>
                        <th class="text-center">ชื่อย่อ</th>
                        <th class="text-left"> ชื่อสาขา</th>
                        <th class="text-left">ที่อยู่</th>
                        <th class="text-center">เบอร์โทรติดต่อ</th>
                        <th class="text-center">ทำรายการ</th>
                    </tr>
                </thead>  
                <tbody>
                    {{-- @foreach ($dropcenters as $dropcenter)
                        <tr>
                            <td class="text-center">{{$dropcenter->drop_center_name_initial}}</td>
                            <td class="text-left">{{$dropcenter->drop_center_name}}</td>
                            <td class="text-left">
                                {{$dropcenter->drop_center_address}}
                                {{$dropcenter->District->name_th}}
                                {{$dropcenter->amphure->name_th}}
                                {{$dropcenter->province->name_th}}
                                {{$dropcenter->drop_center_postcode}}
                            </td>
                            <td class="text-center">{{$dropcenter->drop_center_phone}}</td>
                            <td class="text-center">
                                <a href="/dropcenterArea/{{$dropcenter->id}}">
                                    <button type="button" id="PopoverCustomT-1"
                                        class="btn btn-success btn-sm">เขตในพื้นที่</button>
                                </a>
                                <a href="/dropcenter/{{$dropcenter->id}}">
                                    <button type="button" id="PopoverCustomT-1"
                                        class="btn btn-primary btn-sm">ตั้งค่าใหม่</button>
                                </a>
                            </td>
                        </tr>  
                    @endforeach --}}
                </tbody>
            </table>
            {{-- <br><br> --}}
            <div class="d-block text-left card-footer">
                <div class="row">
                    <div class="col-lg-6 col-md-6 text-left">
                        {{-- <nav class="" aria-label="Page navigation example">
                            <ul class="pagination">
                                <li class="page-item"><a href="javascript:void(0);" class="page-link"
                                        aria-label="Previous"><span aria-hidden="true">«</span><span
                                            class="sr-only">Previous</span></a></li>
                                <li class="page-item"><a href="javascript:void(0);" class="page-link">1</a></li>
                                <li class="page-item active"><a href="javascript:void(0);" class="page-link">2</a>
                                </li>
                                <li class="page-item"><a href="javascript:void(0);" class="page-link">3</a></li>
                                <li class="page-item"><a href="javascript:void(0);" class="page-link"
                                        aria-label="Next"><span aria-hidden="true">»</span><span
                                            class="sr-only">Next</span></a></li>
                            </ul>
                        </nav> --}}
                    </div>
                    <div class="col-lg-6 col-md-6 text-right">
                        <a href="/drop_center_create">
                            <button class="btn-wide  btn btn-primary">เพิ่ม DROP CENTER ใหม่</button>
                        </a>
                        {{-- <button class="btn-wide  btn btn-success">Export Document</button> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script>
    $(function () {
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                method:"POST",
                url:"{{url('dropCenterGetListDataTable')}}",
                dataType: 'json',
                data:{
                        "_token": "{{ csrf_token() }}",
                        "id": "{{ $id }}",
                    },
            },
            columns: [
                {data: 'drop_center_name_initial', name: 'drop_center_name_initial'},
                {data: 'drop_center_name', name: 'drop_center_name'},
                {data: 'drop_center_address', name: 'drop_center_address', className:'text-center'},
                {data: 'drop_center_phone', name: 'drop_center_phone', className:'text-center'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });
    });
</script>