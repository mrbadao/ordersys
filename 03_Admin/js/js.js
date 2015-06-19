function validateDateTime(selector){  // yyyy-mm-dd hh-mm

    var value = selector.val();
    var re = /[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) (2[0-3]|[01][0-9]):[0-5][0-9]$/;

    return value.match(re)? true : false;

}