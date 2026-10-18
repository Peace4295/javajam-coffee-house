
var JavaNode = document.getElementById("qty_java");

JavaNode.addEventListener("change", Cal_Java, false);

function Cal_Java(event){
    var qtyJavaChk = JavaNode;

    var pos = qtyJavaChk.value.search(/^[0-9]*$/);

    if (pos != 0)
    {
        alert("You entered (" + qtyJavaChk.value + ") is not correct. \n"+
            "Only NUMBER are allowed");

        qtyJavaChk.value = "";
        document.getElementById("price_java").value = "";
        qtyJavaChk.focus();
        return false;
    }
   
        var JavaPrice = document.getElementById("java_price").value;
        var Total_Java_Price = JavaPrice * JavaNode.value;
        document.getElementById("price_java").value = parseFloat(Total_Java_Price).toFixed(2); //calc sub total price

        Cal_Total();
}
    


var CafeNode1 = document.getElementById("cafesingle");      //cafenode1 store the price of single shot cafe
var CafeNode2 = document.getElementById("cafedouble");      //cafenode2 store the price of double shot cafe
var CafeNode3 = document.getElementById("qty_cafe");        //cafenode3 store the quantity of the selection (single or double)

CafeNode1.addEventListener("change", Cal_Cafe, false);
CafeNode2.addEventListener("change", Cal_Cafe, false);
CafeNode3.addEventListener("change", Cal_Cafe, false);

function Cal_Cafe(event){
    var qtyCafeChk = CafeNode3;

    var pos = qtyCafeChk.value.search(/^[0-9]*$/);

    if (pos != 0)
    {
        alert("You entered (" + qtyCafeChk.value + ") is not correct. \n"+
            "Only NUMBER are allowed");

        qtyCafeChk.value = "";
        document.getElementById("price_cafe").value = "";
        qtyCafeChk.focus();
        return false;
    }
    if(CafeNode1.checked == true)
    {
        var Total_Cafe_Price = CafeNode1.dataset.price * CafeNode3.value;
        document.getElementById("price_cafe").value = parseFloat(Total_Cafe_Price).toFixed(2); //calc sub total price
    }
    if(CafeNode2.checked == true)
    {
        var Total_Cafe_Price = CafeNode2.dataset.price * CafeNode3.value;
        document.getElementById("price_cafe").value = parseFloat(Total_Cafe_Price).toFixed(2);
    }
    Cal_Total(); //calc total price
}

var CappNode1 = document.getElementById("cappsingle");
var CappNode2 = document.getElementById("cappdouble");
var CappNode3 = document.getElementById("qty_capp");

CappNode1.addEventListener("change", Cal_Capp, false);
CappNode2.addEventListener("change", Cal_Capp, false);
CappNode3.addEventListener("change", Cal_Capp, false);


function Cal_Capp(event){
    var qtyCappChk = CappNode3;
    var pos = qtyCappChk.value.search(/^[0-9]*$/);

    if (pos != 0)
    {
        alert("You entered (" + qtyCappChk.value + ") is not correct. \n"+
            "Only NUMBER are allowed");

        qtyCappChk.value = "";
        document.getElementById("price_capp").value = "";
        qtyCappChk.focus();

        return false;
    }
    if(CappNode1.checked == true)
    {
        var Total_Cafe_Price = CappNode1.dataset.price * CappNode3.value;
        document.getElementById("price_capp").value = parseFloat(Total_Cafe_Price).toFixed(2); //calc sub total price
    }
    if(CappNode2.checked == true)
    {
        var Total_Cafe_Price = CappNode2.dataset.price * CappNode3.value;
        document.getElementById("price_capp").value = parseFloat(Total_Cafe_Price).toFixed(2);
    }
    Cal_Total();
}

function Cal_Total(){
    var JavaPrice = parseFloat(document.getElementById("price_java").value) || 0;
    var CafePrice = parseFloat(document.getElementById("price_cafe").value) || 0;
    var CappPrice = parseFloat(document.getElementById("price_capp").value) || 0;

    var TotalPrice = JavaPrice + CafePrice + CappPrice;

    document.getElementById("total_price").value = TotalPrice.toFixed(2);

}
//enhancement for Q4ii
//explaination: I used traditional Javascript for main calculation functions.
// as an additional approach, i used jquery for the reset order feature. jquery lets me select and modify
//multiple HTML elements using short syntax.

$("#resetOrder").click(function()
{
    $("#qty_java,#qty_cafe,#qty_capp").val("");

    $("#price_java").val("");
    $("#price_cafe").val("");
    $("#price_capp").val("");

    $("#total_price").val("");

    $("#cafesingle,#cappsingle").prop("checked", true);

});

