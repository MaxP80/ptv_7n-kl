function navOptions() {
    xhr = new XMLHttpRequest();
    xhr.open("POST", "http://localhost/JSValAcro/infosSession.php");
    /*xhr.upload.onprogress = function(e) {
        progress.value = e.loaded;
        progress.max = e.total;
    }*/
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 /*La requête est terminée*/ && (xhr.status == 200 || xhr.status == 0)) {
            //Requête entièrement terminé
            var array = decodeReponse(xhr.responseText);
            var is_coach = recupStatut("is_coach", array);
            var profil_parent = recupStatut("profil_parent", array);
            var profil_adherent = recupStatut("profil_adherent", array);
            var in_comite = recupStatut("in_comite", array);
            var is_admin = recupStatut("is_admin", array);
            if (is_admin) {
                document.getElementById('li_adh').style.display = "block";
                document.getElementById('li_parent').style.display = "block";
                document.getElementById('li_valid_profil').style.display = "block";
            }
            if (is_admin || in_comite || is_coach)
                document.getElementById('Messagerie').style.display = "block";
        }
        else {
        }
    }
    var form = new FormData();
    xhr.send(form);
};

function decodeReponse(string){
    var res = string.split('||');
    for (var i = 0; i < res.length; i++){
        res[i] = res[i].split("=");
        try{
            res[i][1] = parseInt(res[i][1], 2);
            
        } catch (error) {
            return null;
        }
    }
    return (res);
    //On retourne le tableau à deux dimensions semblables à une variable de type JSON
}

/*name correspond au nom de l'attricut que l'on souhaite avoir*/
function recupStatut(name, array){
    for (var i = 0; i<array.length; i++){
        if (array[i][0] == name){
            return array[i][1];
        }
    }
    return undefined;
}

$statutUser = navOptions();