			
function vote(issue_id, token){
	jImc.ajax({
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
				jImc(".imc-votes-counter").html(data.votes);
				jImc(".imc-flasher").effect("highlight", {color: '#60FF05'}, 1500);
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

function htmlEscape(str) {
    return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
}

function comment(){
	if(jImc("#imc-comment-area").val() == ''){
		alert(Joomla.JText._('COM_IMPROVEMYCITY_WRITE_COMMENT')); 
		return;
	}
	var base = window.com_improvemycity.base;
	var htmlStr = jImc('#imc-comment-area').val();
	jImc('#imc-comment-area').val(jImc('<div/>').text(htmlStr).html());
	jImc('#commentBtn').hide();
	jImc('#commentIndicator').append('<div id="ajaxBusy"><p><img src="'+base+'/components/com_improvemycity/images/ajax-loader.gif"></p></div>');
	
	jImc.ajax({
		type : 'POST',
		url : 'index.php',
		datatype: 'json',
		data: jImc('#com_improvemycity_comments').serialize(),
		success: function(data){
			jImc('#commentIndicator').remove();
			jImc('#commentBtn').show();
			if (data.comments === undefined){
				alert('Problem sending message (trying to send invalid characters like quotes?)');
				donothing = 1;
			}
			else{
				//create a container for the new comment
				var content = '<div class="imc-chat"><span class="imc-chat-info">'+data.comments.textual_descr+'</span><span class=\"imc-chat-desc\">'+data.comments.description+'</span><div>';
				div = jImc(content).prependTo("#imc-comments-wrapper");
				jImc("#imc-comment-area").val('');
				div.effect("highlight", {color: '#60FF05'}, 1500);
			}
		}

	});
}