function mesFavorisCheck(data, table)
{

    for (let i = 0; i < data.length; i++) {

        table.row.add([
                i+1,
                data[i]['nom'],
                data[i]['prenom'],
                `<td><input type="checkbox" class="inscrireAvec favoris" id="${data[i][0]}"></input></td>`,
            ]).node().id = i;
        
    }
    table.draw();

}

function mesFavorisButton(data, table)
{

    for (let i = 0; i < data.length; i++) {

        table.row.add([
                i+1,
                data[i]['nom'],
                data[i]['prenom'],
                `<td><button id="${data[i][0]}" type="button" class="btn btn-danger retirerFavori favoris">Retirer favori</button></td>`,
            ]).node().id = i;
        

    }
    

}
