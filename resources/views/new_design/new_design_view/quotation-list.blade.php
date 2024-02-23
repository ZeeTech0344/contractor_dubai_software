@extends("old_design.main")


@section("content")
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold">Quotation List</h6>
    </div>

    <div class="p-2 d-flex justify-content-center align-items-center">
        <div class="d-flex ml-auto">
            <label for="" class="p-2 text-info">Search: </label>
            <input type="text" id="search_data_value" name="search_data_value" class="form-control mr-2 text-info">
            <input type="button" class="btn btn-sm btn-warning" id="search_data" name="search_data" value="Search" style="margin-left:5px;">
            <label for="" class="p-2 text-info">From:</label>
            <input type="date" id="from_date" name="from_date" class="form-control mr-2 text-info" onchange="searchData(this)">
            <label for="" class="p-2 text-info">To:</label>
            <input type="date" id="to_date" name="to_date" class="form-control mr-2 text-info" onchange="searchData(this)">
            <label for="" class="p-2 text-info">Status: </label>
            <select name="status" id="status" class="form-control text-info" onchange="searchData(this)">
                <option value="">Select Status</option>
                <option value="0">Not Approved</option>
                <option value="1">Approved</option>
            </select>
        </div>
    </div>
    
    
    
    <div class="card-body">
        <div class="table-responsive">
           
            <table class="table get-list-of-quotation" id="dataTable" width="100%"
                cellspacing="0">
                <thead>
                    <tr>
                        <th> Invoice# </th>
                        <th> Name </th>
                        <th> Phone# </th>
                        <th> Address </th>
                        <th> Date/Time </th>
                        <th class="text-center"> Status </th>
                        <th> Action </th>
                    </tr>
                </thead>

                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>


  @endsection


  @section("script")
<script>



$(document).on("click", ".delete_invoice", function() {

var invoice_no = $(this).data("id");

var element = this;

$.ajax({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    url: "{{ url('delete-invoice') }}",
    type: "GET",
    data: {
        invoice_no: invoice_no
    },
    success: function(data) {

        $(element).parent().parent().parent().parent().fadeOut();


    }
})

})

var quotation_list = $('.get-list-of-quotation').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                // paging: false,
                // "info": false,
                "language": {
                    "infoFiltered": ""
                },

                ajax: {
                    url: "{{ url('get-list-of-quotation') }}",
                    data: function(d) {
                        d.search_data_value = $("#search_data_value").val();
                        d.status = $("#status").val();
                        d.from_date = $("#from_date").val();
                        d.to_date = $("#to_date").val();
                    }
                },

                columns: [
                    
                    {
                        data: 'invoice_no',
                        name: 'invoice_no'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'phone_no',
                        name: 'phone_no'
                    },
                    {
                        data: 'address',
                        name: 'address'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],

                success: function(data) {
                    console.log(data);
                }
            });

            function searchData(e){
                var from_date = $("#from_date").val();
                var to_date = $("#to_date").val();
                var status = $("#status").val();

                console.log(from_date);
                if( (from_date !== "" && to_date !== "") || status !=="" ){
                    quotation_list.draw();
                }
                
            }



            $("#search_data").on("click", function(){

                quotation_list.draw();

            })

</script>

  @endsection

  