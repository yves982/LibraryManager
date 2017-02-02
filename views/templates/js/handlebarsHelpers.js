'use strict';
(function($h){
    $h.registerHelper("moduloIf", function(index, mod, block){
        if(parseInt(index) % mod == 0) {
            return block.fn(this);
        }
    });
    
    $h.registerHelper("add", function(nb1, nb2){
        return parseInt(nb1) + parseInt(nb2);
    });
    
    $h.registerHelper("substract", function(nb1, nb2){
        return parseInt(nb2) - parseInt(nb1);
    });
    
    $h.registerHelper("arrayCnt", function(myArray){
        return myArray.length;
    });
    
    $h.registerHelper("moduloNotIf", function(index, mod, block){
        if(parseInt(index) % mod != 0) {
            return block.fn(this);
        }
    });
    
    $h.registerHelper("for", function(from, to, inc, block){
        var accu = '';
        for(var i=from; i < to; ++i) {
            accu += block.fn(i);
        }
        return accu;
    });
    
    $h.registerHelper("eq", function(a,b){
        return a == b;
    });
    
    $h.registerHelper("currentYear", function(){
        var date = new Date();
        return date.getFullYear();
    });
    
    $h.registerHelper("fileName", function(fullPath){
        var fileNamePos = fullPath.lastIndexOf('\\');
        var fileName = null;
        if(fileNamePos !== -1) {
            fileName = fullPath.substring(fileNamePos +1);
        }
        return fileName;
    });
    
    $h.registerHelper("timestamp", function() {
        return Date.now();
    });
    
    $h.registerHelper("concat", function(str1,str2){
        if(str1 && str2) {
            return str1 + str2;
        } else {
            return '';
        }
    });
}(Handlebars));


