/* tkms.formcheck.js 
 *  APIs use to check form elements
 */

function checkAreEmpty(arry,msg){
    var errCount = 0;
    var errMsg ="";
    for(var i=0;i<arry.size();i++){
        try{
        checkIsEmpty($(arry[i]),msg);
        }catch(e){
            errCount++;
            errMsg+="No."+(i+1)+":"+e.message+"\n";
        }
     }
     return errMsg;
}

function checkIsEmpty(field, msg){
    if(!$(field).val()){
        $(field).addClass("invalidField");
        throw new Error(msg);
    }else{
        $(field).removeClass("invalidField");
    }

}

function checkIsFormat(field, msg, reg){
    if(!reg.test($(field).val())){
        $(field).addClass("invalidField");
        throw new Error(msg);
    }else{
        $(field).removeClass("invalidField");
    }
}

function setInEditClass(field){
    $(field).focus(function(){
        $(this).addClass("inEditField");
    }).blur(function(){
        $(this).removeClass("inEditField");
    });
}
