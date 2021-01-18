@extends("welcome")
@section("content")

<div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="card-header">รายละเอียดบิลขายสินค้าอื่น
        </div>
        <div class="card-body table-responsive">
            <table class='table data-table'>
                <thead>
                    <tr>  
                        <th width="10%" class="text-center">No</th>
                        <th width="80%" class="text-left">Description</th>
                        <th width="10%" class="text-right">Price</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
        <div class="d-block card-footer">
            <div class="row">
                <div class="col-lg-6 col-md-6 text-left">
                    {{-- <ul class="pagination">
                        <li class="page-item"><a href="javascript:void(0);" class="page-link"
                                aria-label="Previous"><span aria-hidden="true">«</span><span
                                    class="sr-only">Previous</span></a></li>
                        <li class="page-item"><a href="javascript:void(0);" class="page-link">1</a></li>
                        <li class="page-item active"><a href="javascript:void(0);" class="page-link">2</a></li>
                        <li class="page-item"><a href="javascript:void(0);" class="page-link">3</a></li>
                        <li class="page-item"><a href="javascript:void(0);" class="page-link"
                                aria-label="Next"><span aria-hidden="true">»</span><span
                                    class="sr-only">Next</span></a></li>
                    </ul> --}}
                    <a href="/bookingList/{{$employee->emp_branch_id}}">
                        <button class="btn-wide  btn btn-light"> กลับ </button>
                    </a>
                </div>
             
                <div class="col-lg-6 col-md-6 text-right"> 
                    <a href="/exportDayOrtherSale">
                        <button class="btn-wide  btn btn-success"> export to excel </button>
                    </a> 
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
                url:"{{url('getSaleOtherListDatatable')}}",
                dataType: 'json',
                data:{
                        "_token": "{{ csrf_token() }}",
                        "branch_id": "{{ $employee->emp_branch_id }}"
                    },
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', className:'text-center'},
                {data: 'sale_other_product_id', name: 'sale_other_product_id'},
                {data: 'sale_other_price', name: 'sale_other_price', className:'text-right'}
            ]
        });
    });
        // table = $('.data-table').DataTable( {
        //     paging: false
        // } );
</script>