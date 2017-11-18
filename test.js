
function test(){

var response = "\n\n\n\nwhile(1);success";

var i=0;
for (var str = response.substr(i,2); str != 'wh'; i++) {
		str = "";
		str = response.substr(i,2);
}

response = response.substr(i+8);

};
