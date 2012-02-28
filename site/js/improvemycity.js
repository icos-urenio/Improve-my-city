$(document).ready(function(){
	$("#toggleMapSize").click(function(event){
        event.preventDefault();
        var height = $("#mapCanvas").height();
        
		var header = 200;
		
        if(height==400) {
            $("#headerbar").hide(1000);
            $("#toolbar").hide(1000);
			$("#mapCanvas").height($(window).height()-header);
			$(".wrapper").animate({width:'98%'}, 1000);			
			$("#maininner").animate({width:'100%'}, 1000, function(){google.maps.event.trigger(map, 'resize');map.setZoom( map.getZoom() );resetBounds();});			
			
			$("#map-size").html("(Επαναφορά χάρτη)");

        }
        else {
			$("#mapCanvas").height(400);
            $("#headerbar").show(1000);
            $("#toolbar").show(1000);
			$(".wrapper").animate({width:'980px'}, 1000);
			$("#maininner").animate({width:'980px'}, 1000, function(){google.maps.event.trigger(map, 'resize');map.setZoom( map.getZoom() );resetBounds();});			
			$("#map-size").html("(Μεγάλος χάρτης)");
        }
		
		$("#content-info").height($("#wrapper-info").height()-50);
		$("#content-filters").height($("#wrapper-filters").height()-50);
    });
	
	$("a[rel='colorbox']").colorbox();
	$("a[class='colorbox']").colorbox();

	$(".info-close").click(function (event) 
	{ 
		event.preventDefault(); 
		$("#wrapper-info").hide(500);
		if(infoWindow != null)
			infoWindow.close();		
		if(infoBox != null)
			infoBox.close();				
		
	});	

	$(".filter-close").click(function (event) 
	{ 
		event.preventDefault(); 
		$("#wrapper-filters").hide(500);
	});	

	$(".filter-open").click(function (event) 
	{ 
		event.preventDefault(); 
		
		if( $('#wrapper-filters').is(':visible') ) {
			$("#wrapper-filters").hide(500);
		}
		else {
			$("#wrapper-filters").show(500);
		}		
	});	
	
	$("#content-info").height($("#wrapper-info").height()-50);
	$("#content-filters").height($("#wrapper-filters").height()-50);
	

});
 
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
				$(".votes-counter").html(data.votes);
				$(".votes").effect("highlight", {color: '#60FF05'}, 1500);
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
	if($("#comment_area").val() == ''){
		alert("Γράψτε πρώτα το σχόλιο σας");
		return;
	}
	
	var htmlStr = $('#comment_area').val(); 
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
				var div = $("<div>").addClass("chat").prependTo("#comments-wrapper");

				//add author name and comment to container
				$("<span class=\"chat-info\">").text(data.comments.textual_descr).appendTo(div);
				$("<span class=\"chat-desc\">").text(data.comments.description).appendTo(div);
				div.effect("highlight", {color: '#60FF05'}, 1500);
				$("#comment_area").val('');
				
			}
		}		
	});
}


