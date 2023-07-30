<?php
require_once('./../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `product_list` where id = '{$_GET['id']}' and delete_flag = 0 ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }else{
?>
		<center>Unknown Business Type</center>
		<style>
			#uni_modal .modal-footer{
				display:none
			}
		</style>
		<div class="text-right">
			<button class="btn btndefault bg-gradient-dark btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
		</div>
		<?php
		exit;
		}
}
?>

<div class="container-fluid">
	<form action="" id="product-form">
		<input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
		<input type="hidden" name ="importer_id" value="<?= $_settings->userdata('id') ?>">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="requestName" class="control-label">Request Name</label>
					<input name="requestName" id="requestName" type="text"class="form-control form-control-sm form-control-border" value="<?php echo isset($requestName) ? $requestName : ''; ?>" required>
				</div>
				<div class="form-group">
				<label for="buyerName" class="control-label">Buyer Name</label>
					<input name="buyerName" id="buyerName" type="text"class="form-control form-control-sm form-control-border" value="<?php echo isset($buyerName) ? $buyerName : ''; ?>" required>			
				</div>
				<div class="form-group">
				<label for="contactNo" class="control-label">Contact Number</label>
					<input name="contactNo" id="contactNo" type="number"class="form-control form-control-sm form-control-border" value="<?php echo isset($contactNo) ? $contactNo : ''; ?>" required>			
				</div>
				<div class="form-group">
					<label for="description" class="control-label">Description</label>
					<textarea name="description" id="description" rows="4"class="form-control form-control-sm rounded-0 summernote" required><?php echo isset($description) ? html_entity_decode($description) : ''; ?></textarea>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="expectedPrice" class="control-label">Expected Price</label>
					<input name="expectedPrice" id="expectedPrice" type="number" step="any" class="form-control form-control-sm form-control-border" value="<?php echo isset($price) ? $price : ''; ?>" required>
				</div>
				<div class="form-group">
					<label for="logo" class="control-label">Photo</label>
					<input type="file" id="logo" name="img" class="form-control form-control-sm form-control-border" onchange="displayImg(this,$(this))" accept="image/png, image/jpeg" <?= !isset($id) ? 'required' : '' ?>>
				</div>
				<div class="form-group col-md-6 text-center">
					<img src="<?= validate_image(isset($image_path) ? $image_path : "") ?>" alt="Product Image" id="cimg" class="border border-gray img-thumbnail">
				</div>
				
			</div>
		</div>
		
	</form>
</div>
<script>
   function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }else{
	        	$('#cimg').attr('src', '<?= validate_image(isset($image_path) ? $image_path : "") ?>');
        }
	}
	$(document).ready(function(){
		$('#uni_modal').on('shown.bs.modal',function(){
			$('#category_id').select2({
				placeholder:'Please Select Categoty Here.',
				width:"100%",
				dropdownParent:$('#uni_modal')
			})
			$('.select2-selection').addClass('form-border');
			$('.summernote').summernote({
		        height: "40vh",
		        toolbar: [
		            [ 'style', [ 'style' ] ],
		            [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
		            [ 'fontname', [ 'fontname' ] ],
		            [ 'fontsize', [ 'fontsize' ] ],
		            [ 'color', [ 'color' ] ],
		            [ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
		            [ 'table', [ 'table' ] ],
		            [ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview', 'help' ] ]
		        ]
		    })
		})
		$('#uni_modal #product-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			 if(_this[0].checkValidity() == false){
				 _this[0].reportValidity();
				 return false;
			 }
			var el = $('<div>')
				el.addClass("alert err-msg")
				el.hide()
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_buyerrequest",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.error(err)
					el.addClass('alert-danger').text("An error occured");
					_this.prepend(el)
					el.show('.modal')
					end_loader();
				},
				success:function(resp){
					if(typeof resp =='object' && resp.status == 'success'){
						location.reload();
					}else if(resp.status == 'failed' && !!resp.msg){
                        el.addClass('alert-danger').text(resp.msg);
						_this.prepend(el)
						el.show('.modal')
                    }else{
						el.text("An error occured");
                        console.error(resp)
					}
					$("html, body").scrollTop(0);
					end_loader()

				}
			})
		})

        
	})
</script>