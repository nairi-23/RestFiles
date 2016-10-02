//var xhr = new XMLHttpRequest();
//
//xhr.open("PUT","/files",false);
//
//
//xhr.send();
//
//if(xhr.status == 200){
//    console.log(xhr);
//}else{
//    console.log("bad request");
//    console.log(xhr.status);
//}
//
//var xmlhttp = new XMLHttpRequest();   
//xmlhttp.open("GET", "/files/6",false);
//xmlhttp.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
//xmlhttp.send(JSON.stringify({name:"John Rambo", time:"2pm"}));
//


var deleted_file_button = document.getElementById("delete");
var updated_file_button = document.getElementById("update");

var eventUtil = {
    addHendler: function (elemet, type, hendler) {
        if (elemet.addEventListener) {
            elemet.addEventListener(type, hendler, false);
        } else if (elemet.attachEvent) {
            elemet.attachEvent("on" + type, hendler);
        } else {
            elemet["on" + type] = hendler;
        }
    }
};


eventUtil.addHendler(deleted_file_button, "click", function () {
    var id = this.getAttribute("data");
    var deleteHttp = new XMLHttpRequest();
    deleteHttp.open("DELETE", "/files/" + id, false);
    deleteHttp.send();
    console.log(deleteHttp.response);
});

eventUtil.addHendler(updated_file_button,"click",function(){
    var data =  document.querySelector("input");
    console.log(data);
});

