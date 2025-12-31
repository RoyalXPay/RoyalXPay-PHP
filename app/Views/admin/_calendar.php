<script src="<?php echo base_url('js/moment.min.js'); ?>"></script>
<script src="<?php echo base_url('js/daterangepicker.min.js'); ?>"></script>

<script type="text/javascript">
$(function() {
    <?php if(isset($fromdate) && isset($todate) ) { ?>
    var fromdate = "<?php echo date("Y-m-d",strtotime($fromdate)); ?>";
    var todate = "<?php echo date("Y-m-d",strtotime($todate)); ?>";

    var start = moment(fromdate);
    var end = moment(todate);
    <?php } else{ ?>
    var start = moment().subtract(29, 'days');
    var end = moment();
    <?php } ?>



    function formatDate(date) {
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2)
            month = '0' + month;
        if (day.length < 2)
            day = '0' + day;

        return [year, month, day].join('-');
    }

    function cb(start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        //$('#reportrange').html(ranges.val());

        $('#startDate').val(formatDate(start));
        $('#endDate').val(formatDate(end));
    }

    $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'Last 90 Days': [moment().subtract(89, 'month'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month')
                .endOf('month')
            ],
            'Last  year': [moment().subtract(1, 'year')],
            'Week to date': [moment().subtract(1, 'week')],
            'Month to date': [moment().subtract(1, 'month')],
            'Quarter to date': [moment().subtract(1, 'quarter')],
            'Year to date': [moment().subtract(1, 'year')],
            //    'All to date': [moment().subtract(1,'days')]
        }
    }, cb);

    cb(start, end);

});
</script>