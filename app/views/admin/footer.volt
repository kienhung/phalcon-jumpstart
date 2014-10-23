    
        <!-- Custom JS Admin -->
        <script type="text/javascript" src="{{ config.application.baseUri }}min/index.php?g=jsAdmin&rev={{ setting.global.jsAdminRev }}"></script>     
        
        </div>
        <!-- /#page-content-wrapper -->
    </div> <!-- end Fluid -->

    <!-- Menu Toggle Script -->
    <script>
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
    </script>