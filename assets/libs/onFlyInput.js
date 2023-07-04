window.saveChoice = function(account, choice, checkbox)
{
	var postData = new Object();
	postData["account"] = account;
	postData["choice"] = choice;
	if(checkbox.checked){
		postData["checked"] = '1';
	}else{
		postData["checked"] = '0';
	}
    var url = Routing.generate('account_onflyupdate2');

    $.post({
        url: url,
        data: postData,
        success: function(response) {
            console.log("saveBit done ");
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log("saveBit fail jqXHR.responseText : " + jqXHR.responseText);
            console.log("saveBit fail textStatus : " + textStatus);
            console.log("saveBit fail errorThrown : " + errorThrown);
        }
    });
}