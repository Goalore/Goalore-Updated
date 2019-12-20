var validEmail = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
var onlychar = /^[a-zA-Z ]+$/;

function errorHandler ( xhr, status, error ) {
	if(error){
		alertify.notify(error, 'error', 5).dismissOthers();
	}else{
		alertify.notify('Something went wrong!', 'error', 5).dismissOthers();
	}
}
jQuery(document).ready(function($) {

	$('#site-header .login-frm').submit(function(e){
		var $this = $(this);
		e.preventDefault();
		var user_login = $.trim($this.find('#user_login').val());
		var user_pass = $.trim($this.find('#user_pass').val());
		var security = $.trim($this.find('#security').val());
		console.log(user_pass);
		if(user_login == ''){
			alertify.notify('Empty Username!', 'error', 5).dismissOthers();
			return false;
		}else if(user_pass == ''){
			alertify.notify('Empty Password!', 'error', 5).dismissOthers();
			return false;
		}else{
			$this.find('button[type="submit"]').attr('disabled',true);
			$.ajax({
		        url: frontendJSobject.ajaxURL,
		        type: 'POST',
		        data: {
		            'action':'login_frm',
		            'user_login':user_login,
		            'user_pass':user_pass,
		            'security':security
		        },
		        success: function( response ) {
		        	var $data = JSON.parse(response);
					if($data.status == 'success'){
						alertify.notify($data.msg, 'success', 5).dismissOthers();
						window.location.href = frontendJSobject.userDashbord;
					}else{
						alertify.notify($data.msg, 'error', 5).dismissOthers();
					}
					$this.find('button[type="submit"]').attr('disabled',false);
		        },
		        error: function( response ) {
		        	alertify.notify('An error occurred', 'error', 5).dismissOthers();
		        },
		    });
		}
	});

	$('#register-frm').submit(function(e){
		e.preventDefault();
		var $this 		= $(this);
		var type = $.trim($this.find('#type').val());
		var full_name = $.trim($this.find('#full_name').val());
		var dob = $.trim($this.find('#dob').val());
		var username = $.trim($this.find('#username').val());
		var email = $.trim($this.find('#email').val());
		var password = $.trim($this.find('#password').val());
		var verify_password = $.trim($this.find('#verify_password').val());
		var country = $.trim($this.find('#country').val());
		var zip_code = $.trim($this.find('#zip_code').val());
		var tc = $this.find('#terms-condition');
		var pp = $this.find('#privacy-policy');
		var security = $.trim($this.find('#security').val());
		var referral_code = $.trim($this.find('#referral_code').val());

		if(type == ''){
			alertify.notify('Select Memeber Type!', 'error', 5).dismissOthers();
			return false;
		}else if(full_name == ''){
			alertify.notify('Enter Full Name !', 'error', 5).dismissOthers();
			return false;
		}else if(!onlychar.test(full_name)){
			alertify.notify('Invalid Full Name. Only Alphabetic Character Allowed!', 'error', 5).dismissOthers();
			return false;
		}else if(dob == ''){
			alertify.notify('Select Date Of Birth!', 'error', 5).dismissOthers();
			return false;
		}else if(username == ''){
			alertify.notify('Enter Username!', 'error', 5).dismissOthers();
			return false;
		}else if(email == ''){
			alertify.notify('Enter Email!', 'error', 5).dismissOthers();
			return false;
		}else if(!validEmail.test(email)){
			alertify.notify('Invalid Email!', 'error', 5).dismissOthers();
			return false;
		}else if(password == ''){
			alertify.notify('Enter Password!', 'error', 5).dismissOthers();
			return false;
		}else if(verify_password == ''){
			alertify.notify('Enter Verify Password!', 'error', 5).dismissOthers();
			return false;
		}else if(verify_password != password){
			alertify.notify("Password didn't match!", 'error', 5).dismissOthers();
			return false;
		}else if(country == ''){
			alertify.notify('Enter Country!', 'error', 5).dismissOthers();
			return false;
		}else if(!onlychar.test(country)){
			alertify.notify('Invalid Country. Only Alphabetic Character Allowed!', 'error', 5).dismissOthers();
			return false;
		}else if(zip_code == ''){
			alertify.notify('Enter Zip Code!', 'error', 5).dismissOthers();
			return false;
		}else if(tc.prop("checked") == false){
			alertify.notify('Please Accept Terms and Conditions!', 'error', 5).dismissOthers();
			return false;
		}else if(pp.prop("checked") == false){
			alertify.notify('Please Accept Privacy Policy!', 'error', 5).dismissOthers();
			return false;
		}else{
			$this.find('button[type="submit"]').attr('disabled',true);
			$.ajax({
		        url: frontendJSobject.ajaxURL,
		        type: 'POST',
		        data: {
		            'action':'register_frm',
		            'security':security,
		            'referral_code':referral_code,
		            'type':type,
					'full_name':full_name,
					'dob':dob,
					'username':username,
					'email':email,
					'password':password,
					'verify_password':verify_password,
					'country':country,
					'zip_code':zip_code,
		        },
		        success: function( response ) {
		        	var $data = JSON.parse(response);
					if($data.status == 'success'){
						alertify.notify($data.msg, 'success', 5).dismissOthers();
						if($data.redirect){
							window.location.href = $data.redirect;
						}else{
							window.location.href = frontendJSobject.userDashbord;
						}
					}else{
						alertify.notify($data.msg, 'error', 5).dismissOthers();
					}
					$this.find('button[type="submit"]').attr('disabled',false);
		        },
		        error: errorHandler
		    });
		}
	});

	$('#user-category-frm').submit(function(e){	
		e.preventDefault();
		var $this = $('.cats:checkbox:checked');
		if($this.length == 0){
		    alertify.notify('At least select one category!', 'error', 5).dismissOthers();
			return false;
		}else{
			$catIDs = [];
			$.each($this, function(){
                $catIDs.push($(this).val());
            });
			console.log($catIDs);
			 $.ajax({
		        url: frontendJSobject.ajaxURL,
		        type: 'POST',
		        data: {
		            'action':'user_category_update',
		            'catIDs':$catIDs
		        },
		        success: function( response ) {
		        	var $data = JSON.parse(response);
					if($data.status == 'success'){
						alertify.notify($data.msg, 'success', 5).dismissOthers();
						window.location.href = frontendJSobject.userDashbord;
					}else{
						alertify.notify($data.msg, 'error', 5).dismissOthers();
					}
		        },
		    });
		}
	});	


	$('#create-goal-frm #category').change(function(){
		var $catID = $(this).val();
		$('#create-goal-frm #sub-category').html('');
		if($catID != ''){
		    $.ajax({
		        url: frontendJSobject.ajaxURL,
		        type: 'POST',
		        data: {
		            'action':'get_goal_subcats',
		            'catID':$catID
		        },
		        success: function( response ) {
		        	var data = JSON.parse(response);
		            $.each(data,function(k,v){
		            	$('#create-goal-frm #sub-category').html($('<option>').val(v.term_id).text(v.name)); 
		            });
		        },
		    });
		}
	});
	$('#create-goal-frm').submit(function(e){
		e.preventDefault();
		var $this = $(this);
		var title = $.trim($this.find('#title').val());
		var type = $.trim($this.find('#type').val());
		var target = $.trim($this.find('#target').val());
		var category = $.trim($this.find('#category').val());
		var subcategory = $.trim($this.find('#sub-category').val());
		var status = $.trim($this.find('#status').val());
		if(title == ''){
			alertify.notify('Please Enter Title!', 'error', 5).dismissOthers();
			return false;
		}else if(target == ''){ 
			alertify.notify('Please Select Target Date!', 'error', 5).dismissOthers();
			return false;
		}else if(category == ''){ 
			alertify.notify('Please Select Category!', 'error', 5).dismissOthers();
			return false;
		}else{

			$this.find('button[type="submit"]').attr('disabled',true);
			var GoalData = $this.serialize();
			$.ajax({
				url: frontendJSobject.ajaxURL,
				type:'POST',
				data:{
					'action':'create_goal',
					'GoalData':GoalData
				},
				success: function(response){
					var $data = JSON.parse(response);
					if(typeof $data =='object'){
						if($data.status == 'success'){
							alertify.notify($data.msg, 'success', 5).dismissOthers();
						}else{
							alertify.notify($data.msg, 'error', 5).dismissOthers();
						}	
					}else{
							alertify.notify(response, 'error', 5).dismissOthers();
					}
					window.setTimeout(function(){location.reload()},1000)
				},
				complete: function(){
					$this.find('button[type="submit"]').attr('disabled',false);
				},
				error: errorHandler
			});
		}
	});

	$('#create-alliances-frm').submit(function(e){
		e.preventDefault();
		var $this = $(this);
		var title = $.trim($this.find('#title').val());
		var objective = $.trim($this.find('#objective').val());
		var status = $.trim($this.find('#status').val());

		if(title == ''){
			alertify.notify('Please Enter Title!', 'error', 5).dismissOthers();
			return false;
		}else if(objective == ''){ 
			alertify.notify('Please Enter Alliance Objective!', 'error', 5).dismissOthers();
			return false;
		}else{
			$this.find('button[type="submit"]').attr('disabled',true);
			var AlliancesData = $this.serialize();
			$.ajax({
				url:frontendJSobject.ajaxURL,
				type:'POST',
				data:{
					'action':'create_alliance',
					'AlliancesData':AlliancesData,
				},
				success: function(response){
					var $data = JSON.parse(response);
					if(typeof $data =='object'){
						if($data.status == 'success'){
							alertify.notify($data.msg, 'success', 5).dismissOthers();
						}else{
							alertify.notify($data.msg, 'error', 5).dismissOthers();
						}	
					}else{
							alertify.notify(response, 'error', 5).dismissOthers();
					}
					window.setTimeout(function(){location.reload()},2000)
				},
			});
		}
	});
	
	$('#add-goal-cat-frm').submit(function(e){
		e.preventDefault();
		var respMsg = $('#add-goal-cat-frm #response');
		respMsg.parent().hide('fast');
		var GoalCatData = $(this).serialize();
		$('#add-goal-cat-frm #submit').attr('disabled',true);
		$.ajax({
			url:frontendJSobject.ajaxURL,
			type:'POST',
			data:{
				'action':'manage_goal_catgory',
				'GoalCatData':GoalCatData 
			},
			success: function(response){
				respMsg.parent().show('fast');
				respMsg.addClass('alert-success').removeClass('alert-danger');
				if(response == 'updated'){
					respMsg.html('Category Updated Successfully');
				}else if(response == 'inserted'){
					respMsg.html('Category Inserted Successfully');
				}else{
					respMsg.html(response).addClass('alert-danger').removeClass('alert-success');
				}
				$('#add-goal-cat-frm #submit').attr('disabled',false);
				window.setTimeout(function(){location.reload()},2000)
			}
		});
	});

	$('#editCategory .edit-link').click(function(){
		var term_id = $(this).data('term_id');
		var term_active = $(this).parent().siblings('.term_active').text();
		var term_name = $(this).parent().siblings('.term_name').text();
		$('#add-goal-cat-frm #term_name').val(term_name);
		$('#add-goal-cat-frm #term_id').val(term_id);
		$('#add-goal-cat-frm #submit').text('Update');

		if(term_active == 'No'){
			$('#add-goal-cat-frm .term_active').filter('[value="1"]').attr('checked', false);;
			$('#add-goal-cat-frm .term_active').filter('[value="0"]').attr('checked', true);;
		}else{
			$('#add-goal-cat-frm .term_active').filter('[value="0"]').attr('checked', false);;
			$('#add-goal-cat-frm .term_active').filter('[value="1"]').attr('checked', true);;
		}

		$('html, body').animate({
	        scrollTop: $("#admin-overview").offset().top
	    }, 500);

	});
	$('#editSubCategory .edit-link').click(function(){
		var term_id = $(this).data('term_id');
		var term_active = $(this).parent().siblings('.term_active').text();
		var term_name = $(this).parent().siblings('.term_name').text();
		var term_parent_id = $(this).parent().siblings('.term_parent_id').data('parent_id');
		$('#add-goal-cat-frm #term_name').val(term_name);
		$('#add-goal-cat-frm #term_id').val(term_id);
		$('#add-goal-cat-frm #parent_id').val(term_parent_id);
		$('#add-goal-cat-frm #submit').text('Update');
		if(term_active == 'No'){
			$('#add-goal-cat-frm .term_active').filter('[value="1"]').attr('checked', false);;
			$('#add-goal-cat-frm .term_active').filter('[value="0"]').attr('checked', true);;
		}else{
			$('#add-goal-cat-frm .term_active').filter('[value="0"]').attr('checked', false);;
			$('#add-goal-cat-frm .term_active').filter('[value="1"]').attr('checked', true);;
		}

		$('html, body').animate({
	        scrollTop: $("#admin-overview").offset().top
	    }, 500);

	});


	$('#tickets-ctn .edit-ticket').click(function(e){
		e.preventDefault();
		var $this = $(this);
		var description = $this.siblings('.description').text();
		var resolution = $this.siblings('.resolution').text();
		var status = $this.data('status');
		var id = $this.data('id');
		$('#ticket-frm-main .ticket_description').val($.trim(description));
		$('#ticket-frm-main .ticket_resolution').val($.trim(resolution));
		$('#ticket-frm-main #status').val(status);
		$('#ticket-frm-main #ticket_id').val(id);

		$('#tickets-ctn').hide('fast',function(){
			$('#ticket-frm-main').show('fast');
		});
	});

	$('#bck-ticket-list').click(function(e){
		e.preventDefault();
		$('#ticket-frm-main .ticket_description').val('');
		$('#ticket-frm-main .ticket_resolution').val('');
		$('#ticket-frm-main #status').val('');
		$('#ticket-frm-main #ticket_id').val('');
		$('#ticket-frm-main').hide('fast',function(){
			$('#tickets-ctn').show('fast');
		});
	});

	$('#submit-ticket').submit(function(e){
		e.preventDefault();
		
		// var description = $('#ticket-frm-main .ticket_description').val();
		var resolution = $('#ticket-frm-main .ticket_resolution').val();
		var status = $('#ticket-frm-main #status').val();
		var id = $('#ticket-frm-main #ticket_id').val();

		var respMsg = $('#submit-ticket #response');
		respMsg.parent().hide('fast');
		$('#submit-ticket #submit').attr('disabled',true);
		$.ajax({
			url:frontendJSobject.ajaxURL,
			type:'POST',
			data:{
				'action':'manage_ticket',
				'resolution':resolution,
				'status':status, 
				'id':id
			},
			success: function(response){
				respMsg.parent().show('fast');
				respMsg.html(response);
				$('#submit-ticket #submit').attr('disabled',false);
				window.setTimeout(function(){location.reload()},2000)
			}
		});
	});

	$('body').on('click','#connection-request',function(e){
		e.preventDefault();
		var $this = $(this);
		var $userID = $this.data('user_id');
		$this.attr('disabled',true);

		$.ajax({
			url:frontendJSobject.ajaxURL,
			type:'POST',
			data:{
				'action':'connection_request',
				'userID':$userID,
			},
			success: function(response){
				var $data = JSON.parse(response);
				if($data.status == 'success'){
					$this.text('Remove Connection Request').attr('id','remove-connection-request').removeClass('btn-blue').addClass('border border-primary');
					alertify.notify($data.msg, 'success', 5).dismissOthers();
				}else{
					alertify.notify($data.msg, 'error', 5).dismissOthers();
				}
				$this.attr('disabled',false);
			}
		});
	});

	$('body').on('click','#remove-connection-request',function(e){
		e.preventDefault();
		var $this = $(this);
		var $userID = $this.data('user_id');
		$this.attr('disabled',true);

		$.ajax({
			url:frontendJSobject.ajaxURL,
			type:'POST',
			data:{
				'action':'remove_connection_request',
				'userID':$userID,
			},
			success: function(response){
				var $data = JSON.parse(response);
				if($data.status == 'success'){
					$this.text('Request Connection').attr('id','connection-request').removeClass('border border-primary').addClass('btn-blue');
					alertify.notify($data.msg, 'success', 5).dismissOthers();
				}else{
					alertify.notify($data.msg, 'error', 5).dismissOthers();
				}
				$this.attr('disabled',false);
			}
		});
	});

	$('body').on('click','#remove-connection',function(e){
		e.preventDefault();
		var $this = $(this);
		var $userID = $this.data('user_id');
		$this.attr('disabled',true);

		$.ajax({
			url:frontendJSobject.ajaxURL,
			type:'POST',
			data:{
				'action':'remove_connection',
				'userID':$userID,
			},
			success: function(response){
				var $data = JSON.parse(response);
				if($data.status == 'success'){
					$this.text('Request Connection').attr('id','connection-request').removeClass('border border-primary').addClass('btn-blue');
					alertify.notify($data.msg, 'success', 5).dismissOthers();
				}else{
					alertify.notify($data.msg, 'error', 5).dismissOthers();
				}
				$this.attr('disabled',false);
			}
		});
	});

	$('body').on('click','#reject-request',function(e){
		e.preventDefault();
		var $this = $(this);
		var $userID = $this.data('user_id');
		$this.attr('disabled',true);
		$this.siblings().attr('disabled',true);
		
		$.ajax({
			url:frontendJSobject.ajaxURL,
			type:'POST',
			data:{
				'action':'reject_request',
				'userID':$userID,
			},
			success: function(response){
				var $data = JSON.parse(response);
				if($data.status == 'success'){
					alertify.notify($data.msg, 'success', 5).dismissOthers();
				}else{
					alertify.notify($data.msg, 'error', 5).dismissOthers();
				}
				// $this.attr('disabled',false);
				window.setTimeout(function(){location.reload()},2000)
			}
		});
	});

	$('body').on('click','#accept-request',function(e){
		e.preventDefault();
		var $this = $(this);
		var $userID = $this.data('user_id');
		$this.attr('disabled',true);
		$this.siblings().attr('disabled',true);
		$.ajax({
			url:frontendJSobject.ajaxURL,
			type:'POST',
			data:{
				'action':'accept_request',
				'userID':$userID,
			},
			success: function(response){
				var $data = JSON.parse(response);
				if($data.status == 'success'){
					alertify.notify($data.msg, 'success', 5).dismissOthers();
				}else{
					alertify.notify($data.msg, 'error', 5).dismissOthers();
				}
				// $this.attr('disabled',false);
				window.setTimeout(function(){location.reload()},2000)
			}
		});
	});

	$('body').on('click','#remove-this-connection',function(e){
		e.preventDefault();
		var $this = $(this);
		var $userID = $this.data('user_id');
		$this.attr('disabled',true);

		$.ajax({
			url:frontendJSobject.ajaxURL,
			type:'POST',
			data:{
				'action':'remove_connection',
				'userID':$userID,
			},
			success: function(response){
				var $data = JSON.parse(response);
				if($data.status == 'success'){
					$this.parent().parent().hide();
					alertify.notify($data.msg, 'success', 5).dismissOthers();
				}else{
					alertify.notify($data.msg, 'error', 5).dismissOthers();
				}
				$this.attr('disabled',false);
			}
		});
	});


	$('body').on('click','#add-milestone-btn',function(e){
		e.preventDefault();
		var label = $(this).siblings('label').text();
		var imgSrc = $(this).parent().siblings('#type-img').attr('src');
		$('#add-goal-data #mc-type-img').attr('src',imgSrc);
		$('#add-goal-data #label').text(label);
		$('#add-goal-data #selector').val('milestones');

		$('#mc-listing').hide('fast',function(){
			$('#add-goal-data').show('fast');
		});
	});
	$('body').on('click','#add-challenge-btn',function(e){
		e.preventDefault();

		var label = $(this).siblings('label').text();
		var imgSrc = $(this).parent().siblings('#type-img').attr('src');
		$('#add-goal-data #mc-type-img').attr('src',imgSrc);
		$('#add-goal-data #label').text(label);
		$('#add-goal-data #selector').val('challenges');
		$('#mc-listing').hide('fast',function(){
			$('#add-goal-data').show('fast');
		});
	});
	$('body').on('click','#back-mc-btn',function(e){
		e.preventDefault();
		$('#add-goal-data button[type="submit"]').text('Create');
		$('#add-goal-data #row_id').val('0');
		$('#add-goal-data').hide('fast',function(){
			$('#mc-listing').show('fast');
		});
	});

	$('#goal-mc-frm').submit(function(e){
		e.preventDefault();
		var $this = $(this);
		var title = $.trim($this.find('#title').val());
		var target = $.trim($this.find('#target').val());
		var status = $.trim($this.find('#status').val());
		var selector = $.trim($this.find('#selector').val());
		var goal_id = $.trim($this.find('#goal_id').val());
		var row_id = $.trim($this.find('#row_id').val());
	
		if(title == ''){
			alertify.notify('Enter Title', 'error', 5).dismissOthers();
			return false;
		}else if(target == ''){
			alertify.notify('Select Target Date', 'error', 5).dismissOthers();
			return false;
		}else{
			$this.find('button[type="submit"]').attr('disabled',true);
			$.ajax({
				url:frontendJSobject.ajaxURL,
				type:'POST',
				data:{
					'action':'add_goal_mc',
					'title':title,
					'target':target,
					'status':status,
					'selector':selector,
					'goal_id':goal_id,
					'row_id':row_id,
				},
				success: function(response){
					var $data = JSON.parse(response);
					if($data.status == 'success'){
						alertify.notify($data.msg, 'success', 5).dismissOthers();
					}else{
						alertify.notify($data.msg, 'error', 5).dismissOthers();
					}
					window.setTimeout(function(){location.reload()},2000)
				},
			});
		}
	});

	$('body').on('click','#edit-ms-btn,#edit-challenge-btn',function(e){
		var $this = $(this);
		var row_id = $this.data('row_id');
		var goal_id = $this.data('goal_id');
		var selector = '';
		var idd = '';
		var imgSrc = $('#add-goal-data #mc-type-img').attr('src');
		if($(this).is('#edit-ms-btn')){
			selector = 'milestones';
			idd = 'edit-ms-btn';
			imgSrc = imgSrc.replace("goal-challenges.svg","goal-milestones.svg");
		}else if($(this).is('#edit-challenge-btn')){
			selector = 'challenges';
			idd = 'edit-challenge-btn';
			imgSrc = imgSrc.replace("goal-milestones.svg","goal-challenges.svg");
		}
		var $title = $this.parents('.modification-links').siblings('#title').text();
		var $status = $this.parents('.modification-links').siblings('#status').find('span').text();
		var $date = $this.parents('.modification-links').siblings('#target').find('span').text();
		var dteSplit = $date.split("-");
		$target = dteSplit[2]+'-'+dteSplit[1] +'-'+dteSplit[0];
		$('#add-goal-data #row_id').val(row_id);
		$('#add-goal-data #selector').val(selector);
		$('#add-goal-data #title').val($title);
		$('#add-goal-data #target').val($target);
		$('#add-goal-data #status').val($status);

		$('#add-goal-data #mc-type-img').attr('src',imgSrc);
		$('#add-goal-data #label').text( 'Update ' + selector.slice(0,-1)).css('textTransform', 'capitalize');
		$('#add-goal-data button[type="submit"]').text('Update');
		$('#mc-listing').hide('fast',function(){
			$('#add-goal-data').show('fast');
		});
	});

	$('body').on('click','#remove-milestone,#remove-challenge',function(e){
		e.preventDefault();
		var $this = $(this);
		var row_id = $this.data('row_id');
		var goal_id = $this.data('goal_id');
		var selector = '';
		var idd = '';
		if($this.is('#remove-milestone')){
			selector = 'milestones';
			idd = 'remove-milestone';
		}else if($this.is('#remove-challenge')){
			selector = 'challenges';
			idd = 'remove-challenge';
		}
		var ll = selector.slice(0,-1)
		alertify.confirm("Delete "+ ll,"Are you sure you want to delete this " + ll, function() {
			$this.parents('.goal-item-child').css('opacity',0.5);
			$this.removeAttr('href id');
			if(selector){
				$.ajax({
					url:frontendJSobject.ajaxURL,
					type:'POST',
					data:{
						'action':'delete_goal_mc',
						'goal_id':goal_id,
						'row_id':row_id,
						'selector':selector,
					},
					success: function(response){
						var $data = JSON.parse(response);
						if($data.status == 'success'){
							$this.parents('.goal-item-child').remove();
							$('#'+selector+' .goal-item-child').each(function(i,v){
								$this.find('a.remove').data('row_id',(i+1));
							});
							alertify.notify($data.msg, 'success', 5).dismissOthers();
						}else{
							alertify.notify($data.msg, 'error', 5).dismissOthers();
						}
					},
					complete: function(){
						$this.parents('.goal-item-child').css('opacity',1);
						$this.attr('id',idd);
			        },
			        error: errorHandler
				});
			}
		}, function() {
	    	
		});
	});

	$('body').on('click','#update-goal-status',function(e){
		e.preventDefault();
		var $this = $(this);
		var goal_id = $this.data('goal_id');
		alertify.confirm("Change Goal Status","Are you sure you want to change goal status?", function() {
	        $this.css('opacity',0.5).removeAttr('id');
			$.ajax({
				url:frontendJSobject.ajaxURL,
				type:'POST',
				data:{
					'action':'update_goal_status',
					'goal_id':goal_id,
				},
				success: function(response){
					var $data = JSON.parse(response);
					if($data.status == 'success'){
						alertify.notify($data.msg, 'success', 5).dismissOthers();
						window.setTimeout(function(){location.reload()},1000);
					}else{
						alertify.notify($data.msg, 'error', 5).dismissOthers();
					}
				},
				complete: function(){
					$this.css('opacity',1).attr('id','update-goal-status');
		        },
		        error: errorHandler
			});
	    },
	    function() {
		    	// alertify.error('Cancel');
		});
	});

	$('body').on('click','#add-pov-btn',function(e){
		e.preventDefault();
		$(this).hide('fast');
		$('#goal-pov-frm').find('#pov_parent_id').val(0)
		$('#pov-listing').hide('fast',function(){
			$('#attachments-ctn').hide('fast');
			$('#pov-main-ctn').show('fast');
		});
	});

	$('body').on('click','#pov-respond-btn',function(e){
		e.preventDefault();
		var $this = $(this);
		var pov_id = $this.data('pov_id');
		$('#goal-pov-frm').find('#pov_parent_id').val(pov_id);

		$('#add-pov-btn').hide('fast');
		$('#pov-listing').hide('fast',function(){
			$('#attachments-ctn').hide('fast');
			$('#pov-main-ctn').show('fast');
		});

	});
	
	$('#goal-pov-frm').submit(function(e){
		e.preventDefault();
		var $this = $(this);
		var description = $.trim($this.find('#description').val());
		var goal_id = $.trim($this.find('#goal_id').val());
		var pov_parent_id = $.trim($this.find('#pov_parent_id').val());
		if(description == ''){
			alertify.notify('Please Add Some Comment!', 'error', 5).dismissOthers();
			return false;
		}else{
			$this.find('button[type="submit"]').attr('disabled',true);
			$.ajax({
				url:frontendJSobject.ajaxURL,
				type:'POST',
				data:{
					'action':'add_goal_pov',
					'description':description,
					'goal_id':goal_id,
					'pov_parent_id':pov_parent_id,
				},
				success: function(response){
					var $data = JSON.parse(response);
					if($data.status == 'success'){
						alertify.notify($data.msg, 'success', 5).dismissOthers();
					}else{
						alertify.notify($data.msg, 'error', 5).dismissOthers();
					}
					window.setTimeout(function(){location.reload()},2000)
				},
			});
		}
	});

	$('body').on('click','#back-pov-btn',function(e){
		e.preventDefault();
		$('#add-attachments-btn').show('fast');
		$('#goal-pov-frm').find('#pov_parent_id').val(0);
		$('#add-pov-btn').show('fast');
		$('#pov-main-ctn').hide('fast',function(){
			$('#pov-listing').show('fast');
			$('#attachments-ctn').hide('fast');	
		});
	});

	$('#pov-listing input[type=radio]').on('change', function() {
		var rating = 0;
		var $this = $(this);
		var thisParent = $this.parents('ul.list-inline');
		thisParent.css('opacity',0.3);
		$('#pov-listing input[type=radio]').prop('disabled', true);
		var pov_id = thisParent.data('pov_id')
		if($this.is(':checked')){
			rating = $this.val();
		}
		$.ajax({
			url:frontendJSobject.ajaxURL,
			type:'POST',
			data:{
				'action':'update_pov_rating',
				'rating':rating,
				'pov_id':pov_id,
			},
			success: function(response){
				var $data = JSON.parse(response);
				if($data.status == 'success'){
					alertify.notify($data.msg, 'success', 5).dismissOthers();
				}else{
					alertify.notify($data.msg, 'error', 5).dismissOthers();
				}
			},
			complete: function() {
				thisParent.css('opacity',1);
				$('#pov-listing input[type=radio]').prop('disabled', false);
	        },
	        error: errorHandler
		});


	});
	$('body').on('click','#unrate-pov',function(e){
		var $this = $(this);
		var thisParent = $this.parents('ul.list-inline');
		var checkedVal = thisParent.find('input[type=radio]:checked');
		var pov_id = thisParent.data('pov_id');
		alertify.confirm("Remove POV","Are you sure you want to delete this POV?",
	    function() {
	    	checkedVal.prop('checked', false);
	    	$('#pov-listing input[type=radio]').prop('disabled', true);
	    	thisParent.css('opacity',0.3);
			$.ajax({
				url:frontendJSobject.ajaxURL,
				type:'POST',
				data:{
					'action':'update_pov_rating',
					'rating':0,
					'pov_id':pov_id,
				},
				success: function(response){
					var $data = JSON.parse(response);
					if($data.status == 'success'){
						alertify.notify($data.msg, 'success', 5).dismissOthers();
						$this.parents('.pov-item-child').remove();
					}else{
						alertify.notify($data.msg, 'error', 5).dismissOthers();
					}
				},
				complete: function() {
					thisParent.css('opacity',1);
					$('#pov-listing input[type=radio]').prop('disabled', false);
		        },
		        error: errorHandler
			});
		},
	    function() {
	    	// alertify.error('Cancel');
	    });
	});

	$('body').on('click','#add-attachments-btn',function(e){
		e.preventDefault();
		$(this).hide('fast');
		$('#pov-listing').hide('fast',function(){
			$('#pov-main-ctn').hide('fast');	
			$('#attachments-ctn').show('fast');
		});
	});
	$('#goal-attachments-frm').submit(function(e){
		e.preventDefault();
		var $this = $(this);
		// var description = $.trim($this.find('#description').val());
		var attachment = $.trim($this.find('#attachment').val());
		if(attachment == ''){
			alertify.notify('Please Select Attachment!', 'error', 5).dismissOthers();
			return false;
		}else{
			$this.find('button[type="submit"]').attr('disabled',true);
			var atchm_frm = new FormData($(this)[0]);
			atchm_frm.append('action', 'add_goal_attachment');
			$.ajax({
				type: 'POST', 
				cache: false,
				contentType: false,
				processData: false,
				url: frontendJSobject.ajaxURL,
				data: atchm_frm,
				success:function(response) {
					var $data = JSON.parse(response);
					if($data.status == 'success'){
						alertify.notify($data.msg, 'success', 5).dismissOthers();
					}else{
						alertify.notify($data.msg, 'error', 5).dismissOthers();
					}
					window.setTimeout(function(){location.reload()},2000)
				}	
			});
		}
	});
	
	$('body').on('click','#remove-attachment',function(e){
		e.preventDefault();
		var $this = $(this);
		var row_id = $this.data('row_id');
		var goal_id = $('#goal-attachments-frm').find('#goal_id').val();
		alertify.confirm("Delete Attachment","Are you sure you want to delete this attachment.", function() {
			$this.parents('tr.attachR').css('opacity','0.5');
			$this.removeAttr('id');
			$.ajax({
				url:frontendJSobject.ajaxURL,
				type:'POST',
				data:{
					'action':'remove_goal_attachment',
					'row_id':row_id,
					'goal_id':goal_id,
				},
				success: function(response){
					var $data = JSON.parse(response);
					if($data.status == 'success'){
						$this.parents('tr.attachR').remove();
						$('#attch-listing tr.attachR').each(function(i,v){
							$(this).find('a').data('row_id',(i+1));
						});
						alertify.notify($data.msg, 'success', 5).dismissOthers();
					}else{
						alertify.notify($data.msg, 'error', 5).dismissOthers();
					}
				},
			});
		}, function() {
		    	// alertify.error('Cancel');
		});
	});

	$('body').on('click','#follow-goal',function(e){
		e.preventDefault();
		var $this = $(this);
		var goal_id = $this.data('goal_id');
		$this.attr('disabled',true);
		$.ajax({
			url:frontendJSobject.ajaxURL,
			type:'POST',
			data:{
				'action':'follow_goal',
				'goal_id':goal_id,
			},
			success: function(response){
				var $data = JSON.parse(response);
				if($data.status == 'success'){
					// $this.attr('id','unfollow-goal').text('Unfollow').removeClass('btn-blue').addClass('border border-primary');
					alertify.notify($data.msg, 'success', 5).dismissOthers();
				}else{
					alertify.notify($data.msg, 'error', 5).dismissOthers();
				}
				// $this.attr('disabled',false);
				location.reload()
			},
		});
	});

	$('body').on('click','#unfollow-goal',function(e){
		e.preventDefault();
		var $this = $(this);
		var goal_id = $this.data('goal_id');
		$this.attr('disabled',true);
		$.ajax({
			url:frontendJSobject.ajaxURL,
			type:'POST',
			data:{
				'action':'unfollow_goal',
				'goal_id':goal_id,
			},
			success: function(response){
				var $data = JSON.parse(response);
				if($data.status == 'success'){
					// $this.attr('id','follow-goal').text('Follow').removeClass('border border-primary').addClass('btn-blue');
					alertify.notify($data.msg, 'success', 5).dismissOthers();
				}else{
					alertify.notify($data.msg, 'error', 5).dismissOthers();
				}
				// $this.attr('disabled',false);
				location.reload()
			},
		});
	});

	$('#user-Settings-frm input[type=radio]').on('change', function() {
		$this = $(this);

		var name = $this.attr('name');
		var value = $this.val();

		$this.parents('.form-check-grp').css('opacity',0.3);
		$('#user-Settings-frm input[name='+name+']').attr('disabled',true);

		$.ajax({
			url:frontendJSobject.ajaxURL,
			type:'POST',
			// async: false,
			data:{
				'action':'update_user_settings',
				'name':name,
				'value':value,
			},
			success: function(response){
				var $data = JSON.parse(response);
				if($data.status == 'success'){
					alertify.notify($data.msg, 'success', 5).dismissOthers();
				}else{
					alertify.notify($data.msg, 'error', 5).dismissOthers();
				}
			},
			complete: function() {
				$this.parents('.form-check-grp').css('opacity',1);
				$('#user-Settings-frm input[name='+name+']').removeAttr('disabled');	
	        },
	        error: errorHandler
		});

	});

	$('body').on('click','#unlinked-goal-btn',function(e){
		$('#linked-goals-list').hide('fast',function(){
			$('#unlink-goal').show('fast')
		});
	});
	$('body').on('click','#back-linked-goals-list-btn',function(e){
		$('#unlink-goal').hide('fast',function(){
			$('#linked-goal').hide('fast');
			$('#linked-goals-list').show('fast')
		});
	});
	$('body').on('click','#link-goal',function(e){
		$('#linked-goals-list').hide('fast',function(){
			$('#linked-goal').show('fast')
		});
	});

	$('body').on('click','#unlink-goal-btn',function(e){
		e.preventDefault();
		var $this = $(this);
		var goal_id = $this.data('goal_id');
		var alliance_id = $this.data('alliance_id');
		$this.parents('.goald-item-item').css('opacity',0.5);
		$this.removeAttr('href id');
			
		$.ajax({
			url:frontendJSobject.ajaxURL,
			type:'POST',
			data:{
				'action':'unlink_goal',
				'goal_id':goal_id,
				'alliance_id':alliance_id,
			},
			success: function(response){
				var $data = JSON.parse(response);
				if($data.status == 'success'){
					$this.parents('.goald-item-item').remove();
					alertify.notify($data.msg, 'success', 5).dismissOthers();
				}else{
					alertify.notify($data.msg, 'error', 5).dismissOthers();
				}
				location.reload()
			},
			complete: function() {
				$this.parents('.goald-item-item').css('opacity',1);
				$this.attr('id','unlink-goal-btn');
	        },
	        error: errorHandler
		});
	});

	$('body').on('click','#link-goal-btn',function(e){
		e.preventDefault();
		var $this = $(this);
		var goal_id = $this.data('goal_id');
		var alliance_id = $this.data('alliance_id');
		$this.parents('.goald-item-item').css('opacity',0.5);
		$this.removeAttr('href id');
			
		$.ajax({
			url:frontendJSobject.ajaxURL,
			type:'POST',
			data:{
				'action':'link_goal',
				'goal_id':goal_id,
				'alliance_id':alliance_id,
			},
			success: function(response){
				var $data = JSON.parse(response);
				if($data.status == 'success'){
					$this.parents('.goald-item-item').remove();
					alertify.notify($data.msg, 'success', 5).dismissOthers();
				}else{
					alertify.notify($data.msg, 'error', 5).dismissOthers();
				}
				location.reload();
			},
			complete: function() {
				$this.parents('.goald-item-item').css('opacity',1);
				$this.attr('id','link-goal-btn');
	        },
	        error: errorHandler
		});

	});




	$('body').on('click','#add-al-btn',function(e){
		$('#action-log-frm').find('label#label').text('Action');
		$('#action-log-frm').find('#al_parent_id').val(0);
		$('#al-listing').hide('fast',function(){
			$('#invite-member-ctn').hide('fast');
			$('#add-al-ctn').show('fast')
		});
	});
	$('body').on('click','#back-al-btn',function(e){
		$('#add-al-ctn').hide('fast',function(){
			$('#invite-member-ctn').hide('fast');
			$('#al-listing').show('fast')
		});
	});


	$('#action-log-frm').submit(function(e){
		e.preventDefault();
		var $this = $(this);
		var description = $.trim($this.find('#description').val());
		var alliance_id = $.trim($this.find('#alliance_id').val());
		var al_parent_id = $.trim($this.find('#al_parent_id').val());
		if(description == ''){
			alertify.notify('Please Add Some Description!', 'error', 5).dismissOthers();
			return false;
		}else{
			$this.find('button[type="submit"]').attr('disabled',true);
			$.ajax({
				url:frontendJSobject.ajaxURL,
				type:'POST',
				data:{
					'action':'add_action_log',
					'description':description,
					'alliance_id':alliance_id,
					'al_parent_id':al_parent_id,
				},
				success: function(response){
					var $data = JSON.parse(response);
					if($data.status == 'success'){
						alertify.notify($data.msg, 'success', 5).dismissOthers();
					}else{
						alertify.notify($data.msg, 'error', 5).dismissOthers();
					}
					location.reload();
				},
				complete: function() {
					$this.find('button[type="submit"]').attr('disabled',false);
		        },
		        error: errorHandler
			});
		}
	});

	$('body').on('click','#al-respond-btn',function(e){
		e.preventDefault();
		var $this = $(this);
		var al_id = $this.data('al_id');
		$('#action-log-frm').find('#al_parent_id').val(al_id);
		$('#action-log-frm').find('label#label').text('Response');
		$('#al-listing').hide('fast',function(){
			$('#invite-member-ctn').hide('fast');
			$('#add-al-ctn').show('fast')
		});

	});



	$('body').on('click','#invite-member-btn',function(e){
		$('#alliance-member-ctn').slideUp('fast');
		$('#alliance-ctn').slideDown('fast');
		$('#al-listing').hide('fast',function(){
			$('#add-al-ctn').hide('fast')
			$('#invite-member-ctn').show('fast');
		});
	});

	



	$('body').on('click','#join-alliance',function(e){
		e.preventDefault();
		var $this = $(this);
		var alliance_id = $this.data('alliance_id');
		$this.attr('disabled',true);
		$.ajax({
			url:frontendJSobject.ajaxURL,
			type:'POST',
			data:{
				'action':'join_alliance',
				'alliance_id':alliance_id,
			},
			success: function(response){
				var $data = JSON.parse(response);
				if($data.status == 'success'){
					alertify.notify($data.msg, 'success', 5).dismissOthers();
				}else{
					alertify.notify($data.msg, 'error', 5).dismissOthers();
				}
				location.reload()
			},
			complete: function() {
				$this.attr('disabled',false);
	        },
	        error: errorHandler
		});
	});

	$('body').on('click','#leave-alliance',function(e){
		e.preventDefault();
		var $this = $(this);
		var alliance_id = $this.data('alliance_id');
		$this.attr('disabled',true);
		$.ajax({
			url:frontendJSobject.ajaxURL,
			type:'POST',
			data:{
				'action':'leave_alliance',
				'alliance_id':alliance_id,
			},
			success: function(response){
				var $data = JSON.parse(response);
				if($data.status == 'success'){
					alertify.notify($data.msg, 'success', 5).dismissOthers();
				}else{
					alertify.notify($data.msg, 'error', 5).dismissOthers();
				}
				location.reload()
			},complete: function() {
				$this.attr('disabled',false);
	        },
	        error: errorHandler
		});
	});


	$('body').on('click','#invite-member',function(e){
		e.preventDefault();
		var $this = $(this);
		var alliance_id = $this.data('alliance_id');
		var user_id = $this.data('user_id');
		$this.parents('.connection-invitation-item').css('opacity',0.5);
		$this.removeAttr('href id');
		$.ajax({
			url:frontendJSobject.ajaxURL,
			type:'POST',
			data:{
				'action':'send_alliance_invitation',
				'alliance_id':alliance_id,
				'user_id':user_id,
			},
			success: function(response){
				var $data = JSON.parse(response);
				if($data.status == 'success'){
					$this.text('Invitation Sent!').attr('id','invitation-sent');
					alertify.notify($data.msg, 'success', 5).dismissOthers();
				}else{
					$this.attr('id','invite-member');
					alertify.notify($data.msg, 'error', 5).dismissOthers();
				}
				// location.reload()
			},complete: function() {
				$this.parents('.connection-invitation-item').css('opacity',1);
	        },
	        error: errorHandler
		});
	});


	$('body').on('click','#invitation-sent',function(e){
		alertify.notify('Alliance Invitation Already Sent!', 'error', 5).dismissOthers();
	});

	$('body').on('click','#show-all-members',function(e){
		$('#alliance-ctn').slideUp('fast',function(){
			$('#alliance-member-ctn').slideDown('fast');
		});
	});
	$('body').on('click','#back-alliance',function(e){
		$('#alliance-member-ctn').slideUp('fast',function(){
			$('#alliance-ctn').slideDown('fast');
		});
	});


	$('body').on('click','#ai-make-admin',function(e){
		e.preventDefault();
		var $this = $(this);
		var user_id = $this.data('user_id');
		var alliance_id = $this.data('alliance_id');
		$this.parents('.connection-invitation-item').css('opacity',0.5);
		$this.removeAttr('href id');
		$.ajax({
			url:frontendJSobject.ajaxURL,
			type:'POST',
			data:{
				'action':'ai_make_admin',
				'alliance_id':alliance_id,
				'user_id':user_id,
			},
			success: function(response){
				var $data = JSON.parse(response);
				if($data.status == 'success'){
					$this.find('label').text('Remove Admin');
					$this.attr('id','ai-remove-admin');
					var newsrc = $this.find('img').attr('src').replace("link-add-icon.svg","unlink-icon.svg");
					$this.find('img').attr('src',newsrc );
					alertify.notify($data.msg, 'success', 5).dismissOthers();
				}else{
					alertify.notify($data.msg, 'error', 5).dismissOthers();
				}
			},complete: function() {
				$this.parents('.connection-invitation-item').css('opacity',1);
	        },
	        error: errorHandler
		});
	});

	$('body').on('click','#ai-remove-admin',function(e){
		e.preventDefault();
		var $this = $(this);
		var user_id = $this.data('user_id');
		var alliance_id = $this.data('alliance_id');
		$this.parents('.connection-invitation-item').css('opacity',0.5);
		$this.removeAttr('href id');
		$.ajax({
			url:frontendJSobject.ajaxURL,
			type:'POST',
			data:{
				'action':'ai_remove_admin',
				'alliance_id':alliance_id,
				'user_id':user_id,
			},
			success: function(response){
				var $data = JSON.parse(response);
				if($data.status == 'success'){
					$this.find('label').text('Make Admin');
					$this.attr('id','ai-make-admin');
					var newsrc = $this.find('img').attr('src').replace("unlink-icon.svg","link-add-icon.svg");
					$this.find('img').attr('src',newsrc );
					alertify.notify($data.msg, 'success', 5).dismissOthers();
				}else{
					alertify.notify($data.msg, 'error', 5).dismissOthers();
				}
			},complete: function() {
				$this.parents('.connection-invitation-item').css('opacity',1);
	        },
	        error: errorHandler
		});
	});


	$('body').on('click','#accept-ai',function(e){
		e.preventDefault();
		var $this = $(this);
		var alliance_id = $this.data('alliance_id');
		$this.parents('.allience-req-opt').css('opacity',0.5);
		$this.removeAttr('href id');
		$.ajax({
			url:frontendJSobject.ajaxURL,
			type:'POST',
			data:{
				'action':'join_alliance',
				'alliance_id':alliance_id,
			},
			success: function(response){
				var $data = JSON.parse(response);
				if($data.status == 'success'){
					$this.parents('tr').remove();;
					alertify.notify($data.msg, 'success', 5).dismissOthers();
				}else{
					alertify.notify($data.msg, 'error', 5).dismissOthers();
				}
				location.reload()
			},complete: function() {
				$this.parents('.allience-req-opt').css('opacity',1);
				$this.attr('id','accept-ai');
	        },
	        error: errorHandler
		});
	});

	$('body').on('click','#reject-ai',function(e){
		e.preventDefault();
		var $this = $(this);
		var alliance_id = $this.data('alliance_id');
		$this.parents('.allience-req-opt').css('opacity',0.5);
		$this.removeAttr('href id');
		$.ajax({
			url:frontendJSobject.ajaxURL,
			type:'POST',
			data:{
				'action':'reject_ai',
				'alliance_id':alliance_id,
			},
			success: function(response){
				var $data = JSON.parse(response);
				if($data.status == 'success'){
					$this.parents('tr').remove();
					alertify.notify($data.msg, 'success', 5).dismissOthers();
				}else{
					alertify.notify($data.msg, 'error', 5).dismissOthers();
				}
			},complete: function() {
				$this.parents('.allience-req-opt').css('opacity',1);
				$this.attr('id','reject-ai');
	        },
	        error: errorHandler
		});
	});


	var typingTimer;
	var doneTypingInterval = 500; 
	var $searchinput = $('#search-member-ai');
	$searchinput.on('keyup', function () {
		clearTimeout(typingTimer);
		typingTimer = setTimeout(callSearchMemberAI, doneTypingInterval);
	});
	$searchinput.on('keydown', function () {
		clearTimeout(typingTimer);
	});

	$('#search-member-ai-frm').submit(function(e){
		e.preventDefault();
		callSearchMemberAI();
	});


	function callSearchMemberAI () {
		var key = $searchinput.val();
		var alliance_id = $searchinput.data('alliance_id');
		$('#my-connections-ai .lds-ring').css('display','inline-block');	
		$.ajax({
			url:frontendJSobject.ajaxURL,
			type:'POST',
			data:{
				'action':'search_member_ai',
				'alliance_id':alliance_id,
				'key':key,
			},
			success: function(response){
				var $data = JSON.parse(response);
				if($data.status == 'success'){
					alertify.notify($data.msg, 'success', 5).dismissOthers();
					$('#my-connections-listing-ai').html($data.membersData);	
				}else{
					alertify.notify($data.msg, 'error', 5).dismissOthers();
				}
			},complete: function() {
				setTimeout(function(){
					$('#my-connections-ai .lds-ring').hide('fast');
				},1000);
	        },
	        error: errorHandler
		});
	}

	$('body').on('click','#deactivate-account',function(e){
		alertify.confirm("Deactivate Account","Are you sure you want to deactivate your account.",
	    function() {
	        $.ajax({
				url:frontendJSobject.ajaxURL,
				type:'POST',
				data:{
					'action':'deactivate_account',
				},
				success: function(response){
					var $data = JSON.parse(response);
					if($data.status == 'success'){
						alertify.notify($data.msg, 'success', 5).dismissOthers();
					}else{
						alertify.notify($data.msg, 'error', 5).dismissOthers();
					}
					location.reload()
				},complete: function() {
					
		        },
		        error: errorHandler
			});
	    },
	    function() {
	    	// alertify.error('Cancel');
	    });
	});
	$('body').on('click','#delete-account',function(e){
		alertify.confirm("Delete Account","Are you sure you want to delete your account.",
	    function() {
	        $.ajax({
				url:frontendJSobject.ajaxURL,
				type:'POST',
				data:{
					'action':'delete_account',
				},
				success: function(response){
					var $data = JSON.parse(response);
					if($data.status == 'success'){
						alertify.notify($data.msg, 'success', 5).dismissOthers();
					}else{
						alertify.notify($data.msg, 'error', 5).dismissOthers();
					}
					location.reload()
				},complete: function() {
					
		        },
		        error: errorHandler
			});
	    },
	    function() {
	    	// alertify.error('Cancel');
	    });
	});

	$('#invite-friend-frm').submit(function(e){
		e.preventDefault();
		var $this = $(this);
		var email = $.trim($this.find('#email').val());
		var subject = $.trim($this.find('#subject').val());
		var message = $.trim($this.find('#message').val());
		if(email == ''){
			alertify.notify('Enter Email!', 'error', 5).dismissOthers();
			return false;
		}else if(!validEmail.test(email)){
			alertify.notify('Invalid Email!', 'error', 5).dismissOthers();
			return false;
		} else if(subject == ''){
			alertify.notify('Enter Subject!', 'error', 5).dismissOthers();
			return false;
		}else if(message == ''){
			alertify.notify('Enter Message!', 'error', 5).dismissOthers();
			return false;
		}else{
			$this.find('button[type="submit"]').attr('disabled',true);
			$.ajax({
				url:frontendJSobject.ajaxURL,
				type:'POST',
				data:{
					'action':'invite_friend',
					'email':email,
					'subject':subject,
					'message':message,
				},
				success: function(response){
					var $data = JSON.parse(response);
					if($data.status == 'success'){
						$this.trigger("reset");
						alertify.notify($data.msg, 'success', 5).dismissOthers();
					}else{
						alertify.notify($data.msg, 'error', 5).dismissOthers();
					}
				},
				complete: function() {
					$this.find('button[type="submit"]').attr('disabled',false);
		        },
		        error: errorHandler
			});
		}
	});

	$('#contact-frm').submit(function(e){
		e.preventDefault();
		var $this = $(this);
		var type = $.trim($this.find('#type').val());
		var description = $.trim($this.find('#description').val());
		if(type == ''){
			alertify.notify('Enter Type!', 'error', 5).dismissOthers();
			return false;
		}else if(description == ''){
			alertify.notify('Enter Description!', 'error', 5).dismissOthers();
			return false;
		}else{
			$this.find('button[type="submit"]').attr('disabled',true);
			$.ajax({
				url:frontendJSobject.ajaxURL,
				type:'POST',
				data:{
					'action':'submit_ticket_user',
					'type':type,
					'description':description,
				},
				success: function(response){
					var $data = JSON.parse(response);
					if($data.status == 'success'){
						$this.trigger("reset");
						alertify.notify($data.msg, 'success', 5).dismissOthers();
					}else{
						alertify.notify($data.msg, 'error', 5).dismissOthers();
					}
				},
				complete: function() {
					$this.find('button[type="submit"]').attr('disabled',false);
		        },
		        error: errorHandler
			});
		}

	});

});

$('body').on('click',"#preview-attachment-btn",function(e){
   $('#attachmentsPreview #img-preview').attr('src',$(this).attr('href'))
});

$('body').on('click',"#edit-profile-btn",function(e){
	var $this = $(this);
	$this.text('Finish Editing').attr('id','finish-editing')
	$('#personal-info-crd').hide('fast',function(){
		$('#edit-profile-frm').show('fast');
		$('#add-pp-btn').show('fast');
	});
});

var pp = $('#edit-profile-frm').find('#profile_picture');
$('#add-pp-btn').on('click', function(e) {
    pp.click();
});

pp.change(function(e) {
    var input = e.target;
    if (input.files && input.files[0]) {
        var file = input.files[0];
        var reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function(e) {
            console.log(reader.result);
            $('#pp-preview').attr('src', reader.result);
        }
    } 
});

$('body').on('click',"#finish-editing",function(e){
		e.preventDefault();
		var $this = $(this);
		var $frm = $('#edit-profile-frm');

		var gender = $.trim($frm.find('#gender').val());
		var country = $.trim($frm.find('#country').val());
		var dob = $.trim($frm.find('#dob').val());
		var profile_picture = $.trim($frm.find('#profile_picture').val());

		if(gender == ''){
			alertify.notify('Select Your Gender!', 'error', 5).dismissOthers();
			return false;
		}else if(country == ''){
			alertify.notify('Enter Country!', 'error', 5).dismissOthers();
			return false;
		}else if(!onlychar.test(country)){
			alertify.notify('Invalid Country. Only Alphabetic Character Allowed!', 'error', 5).dismissOthers();
			return false;
		}else if(dob == ''){
			alertify.notify('Select Date of Birth!', 'error', 5).dismissOthers();
			return false;
		}else{
			$this.attr('disabled',true);
			var atchm_frm = new FormData($frm[0]);
			atchm_frm.append('action', 'update_profile');
			$.ajax({
				type: 'POST', 
				cache: false,
				contentType: false,
				processData: false,
				url: frontendJSobject.ajaxURL,
				data: atchm_frm,
				success:function(response) {
					var $data = JSON.parse(response);
					if($data.status == 'success'){
						alertify.notify($data.msg, 'success', 5).dismissOthers();
					}else{
						alertify.notify($data.msg, 'error', 5).dismissOthers();
					}
				},
				complete: function(){
					window.setTimeout(function(){location.reload()},1000)
		        },
		        error: errorHandler	
			});
		}
	});

