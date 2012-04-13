			
function vote(issue_id, token){
	$.ajax({
		type : 'GET',
		url : 'index.php',
		datatype: 'json',
		data: 'option=com_improvemycity&controller=improvemycity&task=addVote&format=json&issue_id=' + issue_id + '&' + token + '=1',
		success: function(data){
			alert( data.msg );
			if (data.votes === undefined){
				donothing = 1;
			}
			else{
				//update the counter and flash it
				$(".imc-votes-counter").html(data.votes);
				$(".imc-flasher").effect("highlight", {color: '#60FF05'}, 1500);
			}
		}		
	});
}

function dump(arr,level) {
	var dumped_text = "";
	if(!level) level = 0;
	
	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";
	
	if(typeof(arr) == 'object') { //Array/Hashes/Objects 
		for(var item in arr) {
			var value = arr[item];
			
			if(typeof(value) == 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += dump(value,level+1);
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	} else { //Stings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}

function comment(issue_id, token){
	if($("#imc-comment-area").val() == ''){
		alert(Joomla.JText._('COM_IMPROVEMYCITY_WRITE_COMMENT')); 
		return;
	}
	
	var htmlStr = $('#imc-comment-area').val(); 
	description = $('<div/>').text(htmlStr).html(); //trick to get PHP equivalent of htmlentities
	
	$.ajax({
		type : 'GET',
		url : 'index.php',
		datatype: 'json',
		data: 'option=com_improvemycity&controller=improvemycity&task=addComment&format=json&issue_id=' + issue_id + '&' + token + '=1&description=' + description ,
		success: function(data){
			if (data.comments === undefined){
				donothing = 1;
			}
			else{
			
				//create a container for the new comment
				var div = $("<div>").addClass("imc-chat").prependTo("#imc-comments-wrapper");

				//add author name and comment to container
				$("<span class=\"imc-chat-info\">").text(data.comments.textual_descr).appendTo(div);
				$("<span class=\"imc-chat-desc\">").text(data.comments.description).appendTo(div);
				$("#imc-comment-area").val('');
				div.effect("highlight", {color: '#60FF05'}, 1500);
				
				
			}
		}		
	});
}