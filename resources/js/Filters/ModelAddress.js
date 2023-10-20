export default (model, partial = false) => {
    let address = [];

    const line1 = [model.name, model.street].filter(function(line) { 
        return line && line != ''; 
    }).join(' ');

    if(partial) {
        address = [
            line1,
            model.postcode,
        ];
    }
    else {
        address = [
            line1,
            model.town,
            model.city,
            model.county,
            model.postcode,
            model.country
        ];
    }

    return address.filter(function(line) { 
        return line && line != ''; 
    }).join(', ');
}
