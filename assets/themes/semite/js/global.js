$(document).ready(function(){
	init_progress_bars();
	init_datepicker();
	$('.article_useful_buttons button').on('click',function(e){
		e.preventDefault();
		var data = {};
		data.answer = $(this).data('answer');
		data.articleid = $('input[name="articleid"]').val();
		$.post(window.location.href,data).success(function(response){
			response = $.parseJSON(response);
			if(response.success == true){
				$(this).focusout();
			}
			$('.answer_response').html(response.message);
		});
	});
});
function init_progress_bars() {
	setTimeout(function() {
		$('.progress .progress-bar').each(function() {
			var bar = $(this);
			var perc = bar.attr("data-percent");
			var current_perc = 0;
			var progress = setInterval(function() {
				if (current_perc >= perc) {
					clearInterval(progress);
				} else {
					current_perc += 1;
					bar.css('width', (current_perc) + '%');
				}
				bar.text((current_perc) + '%');
			}, 10);
		});

	}, 300);
}
function init_datepicker() {
	$('.datepicker').datepicker({
		autoclose: true,
		format: date_format
	});
	$('.calendar-icon').on('click', function() {
		$(this).parents('.date').find('.datepicker').datepicker('show');
	});
}
// Datatables sprintf language help function
if (!String.prototype.format) {
  String.prototype.format = function() {
    var args = arguments;
    return this.replace(/{(\d+)}/g, function(match, number) {
      return typeof args[number] != 'undefined'
        ? args[number]
        : match
      ;
    });
  };
}

$('#proccess').on('click',function(){

	$('.alert-validation').hide();

	var error = false;
	var cardnumber = $('input[name=\'creditCard[cardNumber]\']');
	var expMonth = $('input[name=\'creditCard[cardExpiryMonth]\']');
	var expYear = $('input[name=\'creditCard[cardExpiryYear]\']');
	var cardCvv = $('input[name=\'creditCard[cardCvv]\']');
	var countryId = $('select[name=\'countryId\']');
	var currencyId = $('select[name=\'currencyId\']');
	var amount = $('input[name=\'amount\']');
	var tracking = $('input[name=\'trackingMemberCode\']');
	var phone = $('input[name=\'additionalInfo[phone]\']');
	var email = $('input[name=\'additionalInfo[email]\']');
	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	var isValidMoney = /^\d{0,4}(\.\d{0,2})?$/.test(amount.val());
	var method = $('select[name=\'method\']');
	var secure = $('input[name=\'secure\']');

	if (cardnumber.val() == ""){
		error = true;
		cardnumber.next().show();
	}

	if (error == false) {

		$.ajax({
			url: site_url + 'terminal/validatepayment',
			type: 'post',
			data: 'cardnumber=' + cardnumber.val(),
			dataType: 'json',
			beforeSend: function () {
			},
			complete: function () {
			},
			success: function (json) {
				if (json.status != "valid"){
					error = true;
					cardnumber.next().show();
				} else {
					cardnumber.next().hide();
				}
			}
		});
	}

	if (expMonth.val() == ""){
		error = true;
		expMonth.next().show();
	}

	if (expYear.val() == ""){
		error = true;
		expYear.next().show();
	}

	if (error == false) {

		var exp_date = expMonth.val()+'/'+expYear.val();
		var status = checkExpire(exp_date);

		if (status !=true){

			error = true;

			$('#general-error').html('Credit Card Expiry date is Invalid!');
			$('#general-error').show();

		} else {
			$('#general-error').hide();
		}
	}

	if (cardCvv.val() == "" || !$.isNumeric(cardCvv.val())){
		error = true;
		cardCvv.next().show();
	}

	if (countryId.val() == ""){
		error = true;
		countryId.next().show();
	}

	if (currencyId.val() == ""){
		error = true;
		currencyId.next().show();
	}

	if (amount.val() == "" || !isValidMoney){
		error = true;
		amount.next().show();
	}

	if (tracking.val() == ""){
		error = true;
		tracking.next().show();
	}

	if (phone.val() != "" &&!$.isNumeric(phone.val())){
		error = true;
		phone.next().show();
	}

	if (email.val() == "" || !email.val().match(re)){
		error = true;
		email.next().show();
	}

		if (error == true){

			return false;
		}


	var data = $('.terminal-form').serialize();

	var process_url = site_url + 'terminal/process';

	if (secure.val() == 1) {
		process_url = site_url + 'terminal/secure_process';


		setTimeout(function () {

			$.ajax({
				url: process_url,
				type: 'post',
				data: data,
				dataType: 'json',
				beforeSend: function () {
					$('.panel-body').hide();
					$('.panel-body').html('<div class="loading"><h4 class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i>  Please wait</h4><p class="text-center">Checking for 3D-Secure Enrollment</p></div>');
					$('.panel-body').show();
				},
				complete: function () {
				},
				success: function (json) {
					$('.loading').hide();


					if (json.hasOwnProperty('avr') && json.avr == 'Y') {

						html = '<html>';
						html += '<head>';
						html += '<title>Semite Payment System 3D-Secure MPI</title>';
						html += '</head>';
						html += '<body>';
						html += '<p>If your browser does not start loading the page, press the button below.';
						html += 'You will be sent back to this site after you authorize the transaction.</p>';
						html += '<button type="submit">Process 3D-Secure Manually</button>';
						html += '<form name="Payer" id="Payer" method="post" action="' + json.url + '">';
						html += '<input type="hidden" name="PaReq" value="' + json.pareq + '" />';
						html += '<input type="hidden" name="TermUrl" value="' + processor_url+'authentication' + '" />';
						html += '<input type="hidden" name="MD" value="' + data + '" />';
						html += '</form>';
						html += '</html>';

						$('.panel-body').html(html);
						$('#Payer').submit();
						return false;

					}

					$.ajax({
						url: site_url + 'terminal/process',
						type: 'post',
						data: data,
						dataType: 'json',
						beforeSend: function () {
							$('.panel-body').hide();
							$('.panel-body').html('<div class="loading"><h4 class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i>  Please wait</h4><p class="text-center">Processing your payment<br/>using <b>Basic Operations</b></p></div>');
							$('.panel-body').show();
						},
						complete: function () {
						},
						success: function (json) {
							$('.loading').hide();
							$('.panel-body').load(site_url + 'terminal/result/' + json.TransactionId +'/' + secure.val());

						}
					});

				}
			});
		}, 200);
		return false;
	} else {
		setTimeout(function () {
			$.ajax({
				url: site_url + 'terminal/process',
				type: 'post',
				data: data,
				dataType: 'json',
				beforeSend: function () {
					$('.panel-body').hide();
					$('.panel-body').html('<div class="loading"><h4 class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i>  Please wait</h4><p class="text-center">Processing your payment<br/>using <b>Basic Operations</b></p></div>');
					$('.panel-body').show();
				},
				complete: function () {
				},
				success: function (json) {
					$('.loading').hide();
					$('.panel-body').load(site_url + 'terminal/result/' + json.TransactionId+'/'+secure.val());

				}
			});
		}, 200);
		return false;
	}

});
// Generate random api access
function generateAccess(element) {

	var url = $(element).attr('data-link');

	setTimeout(function () {
		$.ajax({
			type: 'POST',
			url: url,
			dataType: 'json',
			success: function (json) {

				$('input[name=\'api_id\']').val(json.api_id);
				$('input[name=\'secret_key\']').val(json.secret_key);

			},
			error: function (xhr, ajaxOptions, thrownError) {
				if (xhr.status != 0) {
					alert(xhr.status + "\r\n" +thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			}
		});
	}, 500);
}

function checkExpire(value) {
	var today = new Date();
	var year = value.substring(value.indexOf('/') + 1, 7);

	var month = value.substring(0, value.indexOf('/'));
	var expDate = new Date(year, (month)); // JS Date Month is 0-11 not 1-12 replace parameters with year and month

	if (parseInt(month) > 12) {
		return false;
	} else if (today.getTime() > expDate.getTime()) {
		return false;
	} else if (/^((0[1-9])|(1[0-2]))\/(\d{4})$/i.test(value) == false) {
		return false;
	}

	return true;
}

var salesChart;
var report_from = $('input[name="report-from"]');
var report_to = $('input[name="report-to"]');
var date_range = $('#date-range');

$(document).ready(function() {
	transaction_home_chart();

	$('select[name="currency"]').on('change', function () {
		transaction_home_chart();
	});

	report_from.on('change', function () {
		var val = $(this).val();
		var report_to_val = report_to.val();
		if (val != '') {
			report_to.attr('disabled', false);
			if (report_to_val != '') {
				transaction_home_chart();
			}
		} else {
			report_to.attr('disabled', true);
		}
	});

	report_to.on('change', function () {
		var val = $(this).val();
		if (val != '') {
			transaction_home_chart();
		}
	});

	$('select[name="months-report"]').on('change', function () {
		var val = $(this).val();
		report_to.attr('disabled', true);
		report_to.val('');
		report_from.val('');
		if (val == 'custom') {
			date_range.addClass('fadeIn').removeClass('hide');
			return;
		} else {
			if (!date_range.hasClass('hide')) {
				date_range.removeClass('fadeIn').addClass('hide');
			}
		}
		transaction_home_chart();
	});
});

function transaction_home_chart() {

	// Check if chart canvas exists.
	var chart = $('#transaction-home-chart');
	if(chart.length == 0){
		return;
	}

	if (typeof(salesChart) !== 'undefined') {
		salesChart.destroy();
	}

	var data = {};
	data.months_report = $('select[name="months-report"]').val();
	data.report_from = report_from.val();
	data.report_to = report_to.val();

	var currency = $('#currency');
	if (currency.length > 0) {
		data.report_currency = $('select[name="currency"]').val();
	}

	$.post(site_url + 'clients/transaction_home_chart', data).success(function(response) {
		response = $.parseJSON(response);
		var ctx = chart.get(0).getContext('2d');
		salesChart = new Chart(ctx).Line(response, {
			responsive: true,
			multiTooltipTemplate: "<%= datasetLabel %> - <%= value %>"
		});
	});
}

$(document).ready(function() {

	/* Account Balance */
	var exRowTable3 = $('#account-balance').DataTable({
		responsive: true,
		dom: 'Bfrtip',
		className: 'btn btn-default',
		buttons: [
			'csv', 'excel', 'pdf', 'print'
		],
		'ajax': site_url + 'clients/account_balance',
		'columns': [{
			'class': 'details-control',
			'orderable': false,
			'data': null,
			'defaultContent': ''
		},
			{ 'data': 'type' },
			{ 'data': 'net' },
			{ 'data': 'amount' },
			{ 'data': 'fees' },
			{ 'data': 'description' },
			{ 'data': 'date_added' }
		],
		'order': [[6, 'desc']]
	});

	/* Home List */
	var exRowTable = $('#transaction-log').DataTable({
		responsive: true,
		'ajax': site_url + 'clients/transaction_home_list',
		className: 'btn btn-default',
		paging: false,
		'columns': [{
			'class': 'details-control',
			'orderable': false,
			'data': null,
			'defaultContent': ''
		},
			{ 'data': 'transactionid' },
			{ 'data': 'date_added' },
			{ 'data': 'type' },
			{ 'data': 'cardMask' },
			{ 'data': 'cardType' },
			{ 'data': 'amount' },
			{ 'data': 'status' },
			{ 'data': '3ds' },
		],
		'order': [[2, 'desc']]
	});

	// Add event listener for opening and closing details
	$('#transaction-log tbody').on('click', 'td.details-control', function () {
		var tr = $(this).closest('tr');
		var row = exRowTable.row( tr );

		if ( row.child.isShown() ) {
			// This row is already open - close it
			row.child.hide();
			tr.removeClass('shown');
		} else {
			// Open this row
			row.child( format(row.data()) ).show();
			getDetails(row.data());
			tr.addClass('shown');
		}
	});

	/* All List */

	$( "#min" ).datepicker();
	$( "#max" ).datepicker();

	if (document.getElementById('min')) {
		$.fn.dataTableExt.afnFiltering.push(
			function (oSettings, aData, iDataIndex) {
				var iFini = document.getElementById('min').value;
				var iFfin = document.getElementById('max').value;
				var iStartDateCol = 2;
				var iEndDateCol = 2;

				iFini = iFini.substring(2, 10) + iFini.substring(3, 5) + iFini.substring(0, 2);
				iFfin = iFfin.substring(2, 10) + iFfin.substring(3, 5) + iFfin.substring(0, 2);

				var datofini = aData[iStartDateCol].substring(2, 10) + aData[iStartDateCol].substring(3, 5) + aData[iStartDateCol].substring(0, 2);
				var datoffin = aData[iEndDateCol].substring(2, 10) + aData[iEndDateCol].substring(3, 5) + aData[iEndDateCol].substring(0, 2);

				if (iFini === "" && iFfin === "") {
					return true;
				}
				else if (iFini <= datofini && iFfin === "") {
					return true;
				}
				else if (iFfin >= datoffin && iFini === "") {
					return true;
				}
				else if (iFini <= datofini && iFfin >= datoffin) {
					return true;
				}
				return false;
			}
		);
	}


	var exRowTable2 = $('#transaction-log-list').DataTable({
		responsive: true,
		dom: 'Bfrtip',
		className: 'btn btn-default',
		buttons: [
			'csv', 'excel', 'pdf', 'print'
		],
		'ajax': site_url + 'transactions',
		language: {
			processing: '<div class="dt-loader"></div>'
		},
		processing : true,
		'columns': [{
			'class': 'details-control',
			'orderable': false,
			'data': null,
			'defaultContent': ''
		},
			{ 'data': 'transactionid' },
			{ 'data': 'date_added' },
			{ 'data': 'type' },
			{ 'data': 'cardMask' },
			{ 'data': 'cardType' },
			{ 'data': 'amount' },
			{ 'data': 'status' },
			{ 'data': '3ds' },
		],
		'order': [[2, 'desc']]
	});

	$('#min, #max').change( function() {
		exRowTable2.draw();
	} );

	// Add event listener for opening and closing details
	$('#transaction-log-list tbody').on('click', 'td.details-control', function () {
		var tr = $(this).closest('tr');
		var row = exRowTable2.row( tr );

		if ( row.child.isShown() ) {
			// This row is already open - close it
			row.child.hide();
			tr.removeClass('shown');
		} else {
			// Open this row
			row.child( format(row.data()) ).show();
			getDetails(row.data());
			tr.addClass('shown');
		}
	});

	$('.export').on('click',function(){

		$("#output").html(getTableData($("#transaction-log-list")));
	});

	$('.reset-filter').on('click',function(){

		$( "#min" ).val('');
		$( "#max" ).val('');

		$("#method option:first").prop('selected','selected');
		$("#status option:first").prop('selected','selected');


		$('#method').change( function() { exRowTable2.columns(3).search( this.value).draw() });
		$('#status').change( function() { exRowTable2.columns(7).search( this.value).draw() });

		$("#min").change ( function() { exRowTable2.draw(); } );
		$("#max").change ( function() { exRowTable2.draw(); } );

		$( "#min" ).trigger('change');
		$( "#max" ).trigger('change');

		$( "#method" ).trigger('change');
		$( "#status" ).trigger('change');
		exRowTable2.search( '', true ).draw();

	});

	$('.refresh-data').on('click',function(){
		exRowTable2.ajax.reload();
	});

	$('#method').on('change', function() { exRowTable2.columns(3).search( this.value).draw() });
	$('#status').on('change', function() { exRowTable2.columns(7).search( this.value).draw() });

	function format (d) {

		return '<div id="transaction-'+ d.transactionid+'"><h4 class="loading text-center text-muted"><i class="fa fa-spinner fa-spin"></i>  Please wait</h4></div>';

	}

	function getDetails(data){

		setTimeout(function () {
			$.ajax({
				type: 'POST',
				url: site_url +'transactions/transaction/'+data.transactionid,
				data : data,
				dataType: 'html',
				success: function (html) {

					$('.loading').remove();

					$('#transaction-'+data.transactionid).html(html);
				},
				error: function (xhr, ajaxOptions, thrownError) {
					if (xhr.status != 0) {
						alert(xhr.status + "\r\n" +thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				}
			});
		},200);

	}
});
