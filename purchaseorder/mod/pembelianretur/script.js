// autocomplet : this function will be executed every time we change the text
function autocomplet() {
	var min_length = 0; // min caracters to display the autocomplete
	var keyword = $('#country_id').val();
	if (keyword.length >= min_length) {
		$.ajax({
			url: 'mod/pembelianretur/supplier_refresh.php',
			type: 'POST',
			data: {keyword:keyword},
			success:function(data){
				$('#country_list_id').show();
				$('#country_list_id').html(data);
			}
		});
	} else {
		$('#country_list_id').hide();
	}
}

// set_item : this function will be executed when we select an item
function set_item(item) {
	// change input value
	$('#country_id').val(item);
	// hide proposition list
	$('#country_list_id').hide();
}

// Barang
function autocomplet2() {
	var min_length = 0; // min caracters to display the autocomplete
	var keyword = $('#barang_id').val();
	if (keyword.length >= min_length) {
		$.ajax({
			url: 'mod/pembelianretur/barang_refresh.php',
			type: 'POST',
			data: {keyword:keyword},
			success:function(data){
				$('#barang_list_id').show();
				$('#barang_list_id').html(data);
			}
		});
	} else {
		$('#barang_list_id').hide();
	}
}

function set_item2(item) {
	// change input value
	$('#barang_id').val(item);
	// hide proposition list
	$('#barang_list_id').hide();
}
$(document).ready(function() {
$('#bayar').keyup(function(){
var bayar=parseInt($('#bayar').val());
var total=parseInt($('#total').val());	
var discount=parseInt($('#discount').val());	
var total_bayar=bayar-(total-discount);
$('#Tbayar').show();
$('#Tbayar').html("Kembali : Rp."+total_bayar);
			});
$('#discount').keyup(function(){
var bayar=parseInt($('#bayar').val());
var total=parseInt($('#total').val());	
var discount=parseInt($('#discount').val());		
var total_bayar=(total-discount);
$('#bayar').val(total_bayar);
$('#Tbayar').html("Total : Rp."+total_bayar);
		});

})	
	
//INV
function autocompletinv() {
	var min_length = 0; // min caracters to display the autocomplete
	var keyword = $('#inv_id').val();
	if (keyword.length >= min_length) {
		$.ajax({
			url: 'mod/pembelianretur/inv_refresh.php',
			type: 'POST',
			data: {keyword:keyword},
			success:function(data){
				$('#inv_list_id').show();
				$('#inv_list_id').html(data);
			}
		});
	} else {
		$('#inv_list_id').hide();
	}
}

// set_item : this function will be executed when we select an item
function set_iteminv(item) {
	// change input value
	$('#inv_id').val(item);
	// hide proposition list
	$('#inv_list_id').hide();
}

//RETUR
function autocompletretur() {
	var min_length = 0; // min caracters to display the autocomplete
	var keyword = $('#retur_id').val();
	if (keyword.length >= min_length) {
		$.ajax({
			url: 'mod/pembelianretur/retur_refresh.php',
			type: 'POST',
			data: {keyword:keyword},
			success:function(data){
				$('#retur_list_id').show();
				$('#retur_list_id').html(data);
			}
		});
	} else {
		$('#retur_list_id').hide();
	}
}

// set_item : this function will be executed when we select an item
function set_itemretur(item) {
	// change input value
	$('#retur_id').val(item);
	// hide proposition list
	$('#retur_list_id').hide();
}