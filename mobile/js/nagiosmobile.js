// nagiosmobile.js
// @author Hirose Masaaki
// modified BASEURL variable from original script and moved to separate JS file -MG

$(document).ready(function(){
    var TN_DEBUG = true;
    var debug = TN_DEBUG ? console : { log: function(){}, debug: function(){}, warn: function(){}, info: function(){} };

    $('.submitDowntimeButton').live('click', function(e){
        e.stopPropagation();
        var that = $(this);

        var dt_id = that.data("dt_id");
        var form_id = '#dt_' + dt_id + '_form';
        var form = $(form_id);
        var result_id = '#dt_' + dt_id + '_result';
        debug.log("dt_id: "+dt_id);
        debug.log("form_id: "+form_id);
        debug.log("result_id: "+result_id);

        var dt_host     = form.data("dt_host");
        var dt_comment ;
        var dt_end_date;
        var dt_end_time;


        var dt_service  = form.data("dt_service");
        var dt_comment;
        var dt_comment  = form.find('#dt_'+dt_id+'_comment').val();
        var dt_end_date = form.find('#dt_'+dt_id+'_end_date option:selected').val();
        var dt_end_time = form.find('#dt_'+dt_id+'_end_time option:selected').val();
        debug.log("host: "+dt_host+", service: "+dt_service+", comment: "+dt_comment+", end_date: "+dt_end_date+", end_time: "+dt_end_time);
		//alert("page: schedule_downtime, host: "+dt_host+", service: "+dt_service+", comment: "+dt_comment+", end_date: "+dt_end_date+", end_time: "+dt_end_time);
		//return; 

        that.addClass('ui-disabled')
            .removeClass('ui-btn-active');
        $(result_id).html('<p>Processing...</p>');
        $.ajax({
            type: 'GET',
            cache: false,
            url: BASEURL+'/index.php', //modified for Nagios Mobile -MG
            dataTYpe: 'json',
            data: {
                host     : dt_host,
                service  : dt_service,
                comment  : dt_comment,
                end_date : dt_end_date,
                end_time : dt_end_time,
                page     : 'schedule_downtime'
            },
            success: function(ret) {
                $(result_id).html('<p>Done</p>');
                that.removeClass('ui-disabled');
            },
            error: function(xhr) {
                $(result_id).html('<p>Error ('+xhr.code+')</p>');
                that.removeClass('ui-disabled');
            }
        });

        return false;
    });
	
	
    $('.submitAckProblemButton').live('click', function(e){
        e.stopPropagation();
        var that = $(this);

        var ack_id = that.data("ack_id");
        var form_id = '#ack_' + ack_id + '_form';
        var form = $(form_id);
        var result_id = '#ack_' + ack_id + '_result';
        debug.log("ack_id: "+ack_id);
        debug.log("form_id: "+form_id);
        debug.log("result_id: "+result_id);

        var ack_host     = form.data("ack_host");
        var ack_comment ;



        var ack_service  = form.data("ack_service");
        var ack_comment
        var ack_comment  = form.find('#ack_'+ack_id+'_comment').val();

        debug.log("host: "+ack_host+", service: "+ack_service+", comment: "+ack_comment);

        that.addClass('ui-disabled')
            .removeClass('ui-btn-active');
        $(result_id).html('<p>Processing...</p>');
        $.ajax({
            type: 'GET',
            cache: false,
            url: BASEURL, //modified for Nagios Mobile -MG
            dataTYpe: 'json',
            data: {
                host     : ack_host,
                service  : ack_service,
                comment  : ack_comment,
                page     : 'acknowledge_problem'
            },
            success: function(ret) {
                $(result_id).html('<p>Done</p>');
                that.removeClass('ui-disabled');
            },
            error: function(xhr) {
                $(result_id).html('<p>Error ('+xhr.code+')</p>');
                that.removeClass('ui-disabled');
            }
        });

        return false;
    });
	
	
	
	$('.submitRemoveAckButton').live('click', function(e){
        e.stopPropagation();
        var that = $(this);

        var ack_id = that.data("ack_id");
        var form_id = '#ack_' + ack_id + '_form';
        var form = $(form_id);
        var result_id = '#ack_' + ack_id + '_result';
        debug.log("ack_id: "+ack_id);
        debug.log("form_id: "+form_id);
        debug.log("result_id: "+result_id);

        var ack_host     = form.data("ack_host");
        var ack_comment ;



        var ack_service  = form.data("ack_service");
        var ack_comment
        var ack_comment  = form.find('#ack_'+ack_id+'_comment').val();

        debug.log("host: "+ack_host+", service: "+ack_service+", comment: "+ack_comment);

        that.addClass('ui-disabled')
            .removeClass('ui-btn-active');
        $(result_id).html('<p>Processing...</p>');
        $.ajax({
            type: 'GET',
            cache: false,
            url: BASEURL, //modified for Nagios Mobile -MG
            dataTYpe: 'json',
            data: {
                host     : ack_host,
                service  : ack_service,
                comment  : ack_comment,
                page     : 'remove_acknowledgement'
            },
            success: function(ret) {
                $(result_id).html('<p>Done</p>');
                that.removeClass('ui-disabled');
            },
            error: function(xhr) {
                $(result_id).html('<p>Error ('+xhr.code+')</p>');
                that.removeClass('ui-disabled');
            }
        });

        return false;
    });
	
	
	$('.submitDisnotificationButton').live('click', function(e){
        e.stopPropagation();
        var that = $(this);

        var nt_id = that.data("nt_id");
        var form_id = '#nt_' + nt_id + '_form';
        var form = $(form_id);
        var result_id = '#nt_' + nt_id + '_result';
        debug.log("nt_id: "+nt_id);
        debug.log("form_id: "+form_id);
        debug.log("result_id: "+result_id);

        var nt_host     = form.data("nt_host");

        var nt_service  = form.data("nt_service");

        that.addClass('ui-disabled')
            .removeClass('ui-btn-active');
        $(result_id).html('<p>Processing...</p>');
        $.ajax({
            type: 'GET',
            cache: false,
            url: BASEURL, //modified for Nagios Mobile -MG
            dataTYpe: 'json',
            data: {
                host     : nt_host,
                service  : nt_service,
                page     : 'disable_notification'
            },
            success: function(ret) {
                $(result_id).html('<p>Done</p>');
                that.removeClass('ui-disabled');
            },
            error: function(xhr) {
                $(result_id).html('<p>Error ('+xhr.code+')</p>');
                that.removeClass('ui-disabled');
            }
        });

        return false;
    });
	
	$('.submitEnnotificationButton').live('click', function(e){
        e.stopPropagation();
        var that = $(this);

        var nt_id = that.data("nt_id");
        var form_id = '#nt_' + nt_id + '_form';
        var form = $(form_id);
        var result_id = '#nt_' + nt_id + '_result';
        debug.log("nt_id: "+nt_id);
        debug.log("form_id: "+form_id);
        debug.log("result_id: "+result_id);

        var nt_host     = form.data("nt_host");

        var nt_service  = form.data("nt_service");

        that.addClass('ui-disabled')
            .removeClass('ui-btn-active');
        $(result_id).html('<p>Processing...</p>');
        $.ajax({
            type: 'GET',
            cache: false,
            url: BASEURL, //modified for Nagios Mobile -MG
            dataTYpe: 'json',
            data: {
                host     : nt_host,
                service  : nt_service,
                page     : 'enable_notification'
            },
            success: function(ret) {
                $(result_id).html('<p>Done</p>');
                that.removeClass('ui-disabled');
            },
            error: function(xhr) {
                $(result_id).html('<p>Error ('+xhr.code+')</p>');
                that.removeClass('ui-disabled');
            }
        });

        return false;
    });
});
