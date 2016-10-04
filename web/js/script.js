//var deleted_file_button = document.getElementById("delete");
//var updated_file_button = document.getElementById("update");
//
//var eventUtil = {
//    addHendler: function (elemet, type, hendler) {
//        if (elemet.addEventListener) {
//            elemet.addEventListener(type, hendler, false);
//        } else if (elemet.attachEvent) {
//            elemet.attachEvent("on" + type, hendler);
//        } else {
//            elemet["on" + type] = hendler;
//        }
//    }
//};

//eventUtil.addHendler(deleted_file_button, "click", function () {
//    var id = this.getAttribute("data");
//    var deleteHttp = new XMLHttpRequest();
//    deleteHttp.open("DELETE", "/files/" + id, false);
//    deleteHttp.send();
//    console.log(deleteHttp.response);
//});
//
//eventUtil.addHendler(updated_file_button,"click",function(){
//    var data =  document.querySelector("input");
//    console.log(data);
//});

