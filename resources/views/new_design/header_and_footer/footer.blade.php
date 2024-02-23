
{{-- compulsory
<!-- jQuery -->
<script src="{{url('new_design/plugins/jquery/jquery.min.js')}}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{url('new_design/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
$.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{url('new_design/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- ChartJS -->
<script src="{{url('new_design/plugins/chart.js/Chart.min.js')}}"></script>

<!-- jQuery Knob Chart -->
<script src="{{url('new_design/plugins/jquery-knob/jquery.knob.min.js')}}"></script>
<!-- daterangepicker -->
<script src="{{url('new_design/plugins/moment/moment.min.js')}}"></script>
<script src="{{url('new_design/plugins/daterangepicker/daterangepicker.js')}}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{url('new_design/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
<!-- Summernote -->
<script src="{{url('new_design/plugins/summernote/summernote-bs4.min.js')}}"></script>
<!-- overlayScrollbars -->
<script src="{{url('new_design/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{url('new_design/dist/js/adminlte.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{url('new_design/dist/js/demo.js')}}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{url('new_design/dist/js/pages/dashboard.js')}}"></script>
<script src="{{ url('plugins/select2/js/select2.full.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>



<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/1.13.2/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.4/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.bootstrap5.min.js"></script>
<script src="https://unpkg.com/nprogress@0.2.0/nprogress.js"></script>


</body>
</html>

@yield("script")

<script>



function viewModal(url) {
        if (url) {
            $.ajax({
                url: url,
                type: "GET",
                success: function(data) {
                    $('#modal-default').modal('show');
                    $('#modal-default-title').html(data["title"]);
                    $('#modal-default-body').html(data["view"]);

                }
            })
        }
    }


function successAlert(message){

toastr.options = {
                        closeButton: true,
                        newestOnTop: false,
                        progressBar: true,
                        positionClass: 'toast-top-right',
                        // preventDuplicates: true,
                        showDuration: 300,
                        hideDuration: 1000,
                        timeOut: 1000,
                        extendedTimeOut: 1000,
                        showEasing: 'swing',
                        hideEasing: 'linear',
                        showMethod: 'fadeIn',
                        hideMethod: 'fadeOut',
                        // Customize font size and color
                        "progressBar": true,
                        "positionClass": "toast-top-right",
                        "fontSize": "30px",
                        "fontColor": "#FFFFFF",
                    };
                    // Example notification
                    toastr.info(message);       
}


function errorAlert(message){

toastr.options = {
                        closeButton: true,
                        newestOnTop: false,
                        progressBar: true,
                        positionClass: 'toast-top-right',
                        // preventDuplicates: true,
                        showDuration: 300,
                        hideDuration: 1000,
                        timeOut: 1000,
                        extendedTimeOut: 1000,
                        showEasing: 'swing',
                        hideEasing: 'linear',
                        showMethod: 'fadeIn',
                        hideMethod: 'fadeOut',
                        // Customize font size and color
                        "progressBar": true,
                        "positionClass": "toast-top-right",
                        "fontSize": "30px",
                        "fontColor": "#FFFFFF",
                    };
                    // Example notification
                    toastr.warning(message);       
}



// $(document).ready(function() {
//     NProgress.start();
//     console.log("N progress started");

//     $(document).ajaxStart(function() {
//         NProgress.start();
//         console.log("N progress");
//     });

//     $(document).ajaxComplete(function() {
//         NProgress.done();
//     });
// });

    
</script> --}}




</div><!-- /.container-fluid -->
</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->
<footer class="main-footer">
<strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong>
All rights reserved.
<div class="float-right d-none d-sm-inline-block">
  <b>Version</b> 3.2.0
</div>
</footer>

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
<!-- Control sidebar content goes here -->
</aside>
<!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{url('new_design/plugins/jquery/jquery.min.js')}}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{url('plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
$.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{url('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- ChartJS -->
<script src="{{url('plugins/chart.js/Chart.min.js')}}"></script>
<!-- Sparkline -->
<script src="{{url('plugins/sparklines/sparkline.js')}}"></script>
<!-- JQVMap -->
<script src="{{url('plugins/jqvmap/jquery.vmap.min.js')}}"></script>
<script src="{{url('plugins/jqvmap/maps/jquery.vmap.usa.js')}}"></script>
<!-- jQuery Knob Chart -->
<script src="{{url('plugins/jquery-knob/jquery.knob.min.js')}}"></script>
<!-- daterangepicker -->
<script src="{{url('plugins/moment/moment.min.js')}}"></script>
<script src="plugins/daterangepicker/daterangepicker.js')}}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{url('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
<!-- Summernote -->
{{-- <script src="{{url('plugins/summernote/summernote-bs4.min.js')}}"></script> --}}

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.0-rc.2/dist/quill.js"></script>

<!-- overlayScrollbars -->
<script src="{{url('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{url('dist/js/adminlte.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{url('dist/js/demo.js')}}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{url('dist/js/pages/dashboard.js')}}"></script>
</body>
</html>
