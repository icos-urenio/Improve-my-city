$(document).ready(function(){
	$.ajax({
		type : 'GET',
		url : 'post.php',
		datatype: 'json',
		data: 'option=com_improvemycity&controller=improvemycity&task=addComment&format=json',
		success: function(data){
			alert( "Data Saved: " + data.msg );
		}			
	});		
});



