
try{
//Update UI;
$(document).ready(function(){
	try{
	var chapters = $("H1");
	var sections = $("H2");
	var paragraphs = $("H3");
	var catgory = $("#St1DocCatgory");
	var htmlstr = "";
	var htmlstr2 ="";
	var showLevels=2;
	var showLevel1=(showLevels>=1?true:false);
	var showLevel2=(showLevels>=2?true:false);
	var showLevel3=(showLevels>=3?true:false);
	var borderVisible=true;
	$("#St1DocTitle").html($("#St1DocTitle").html()+"<a id=\"St1DocTop\" />");

	for(var i=0; i<chapters.length; i++){
		$(chapters[i]).html("<a id=\""+$(chapters[i]).text()+"\">"+$(chapters[i]).text()+"</a>");
	}
	for(var i=0; i<sections.length; i++){
		$(sections[i]).html("<a id=\""+$(sections[i]).text()+"\">"+$(sections[i]).text()+"</a>");
	}
	for(var i=0; i<paragraphs.length; i++){
		$(paragraphs[i]).html("<a id=\""+$(paragraphs[i]).text()+"\">"+$(paragraphs[i]).text()+"</a>");
	}	

	for(var i=0;showLevel1 && (i<chapters.length);i++){
		var chapterDiv = chapters[i];
		while($(chapterDiv).attr("class")!="St1DocChapter"){
			chapterDiv = $(chapterDiv).parent();
		}
		if(showLevels==1) chapterDiv.append("<div class=\"St1LinkBackToTop\"><a href=\"#top\">Back to top</a></div>");
		sections = $(chapterDiv).find("H2");
		htmlstr="";
		for(var j=0;showLevel2 && j<sections.length;j++){
			var sectionDiv = sections[j];
			while($(sectionDiv).attr("class")!="St1DocSection"){
				sectionDiv = $(sectionDiv).parent();
			}
			if(showLevels==2) sectionDiv.append("<div class=\"St1LinkBackToTop\"><a href=\"#top\">Back to top</a></div>");			
			paragraphs = $(sectionDiv).find("H3");

			htmlstr2="";
			for(var k=0;showLevel3 && (k<paragraphs.length);k++){
				htmlstr2+="<div class=\"St1CatParagraph\"><a href=\"#"+$(paragraphs[k]).text()+"\">"+$(paragraphs[k]).text()+"</a></div>";
			}

			htmlstr+="<div class=\"St1CatSection\"><a href=\"#"+$(sections[j]).text()+"\">"+$(sections[j]).text()+htmlstr2+"</a></div>";
		}
		$(catgory).append("<div class=\"St1CatChapter\"><a href=\"#"+$(chapters[i]).text()+"\">"+$(chapters[i]).text()+htmlstr+"</a></div>");
	}
	}catch(e){alert(e.message);}  

	//if(borderVisible)
	//$("div").css("border","none");
	$(".St1DocWarning").corner();
	$(".St1DocHint").corner();	
	$(".St1DocWarning").css("border","5px double gold");
	$(".St1DocHint").css("border","5px double green");

});

//Event handlers;	
}catch(e){alert(e.message);}

