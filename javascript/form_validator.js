// i metodi che controllano un dato cominciano il check_nomecampo
// i metodi che controlano un form, ovvero tanti campi, hanno il nome validazione_ecc.

//funzione generale per mostrare errore
function mostra_errore(input, testoErrore){
    togli_errore(input);
    const p = input.parentNode;
    const strong = document.createElement("strong");
    strong.appendChild(document.createTextNode(testoErrore));
    p.appendChild(strong);
}

//da richiamare sempre prima di mostraErrore
function togli_errore(input){
    const p = input.parentNode;
    if(p.children.length > 2){
        p.removeChild(p.children[2]);
    }
}

function check_immagini() {

}
function check_citta(n) {
    const reg = new RegExp('^[a-zA-Z]{3,}$');
    if (reg.test(n.value)){
        togli_errore(n);
        return true;
    }
    mostra_errore(n, "Il nome della citt&agrave; non va bene.")
}
function check_data(data) {
    const reg = new RegExp('');

}
function  check_dateInizioEFine(dataInizio, dataFine) {

}
function check_numOspiti(num) {

}
function check_numeroTelefonico() {

}
function check_userMail(mailInput) {
    const reg = new RegExp('^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$');

    if (reg.test(mailInput.value)){
        togli_errore(mailInput);
        return true;
    }
    mostra_errore(mailInput,"La mail inserita valida!");
    return false;

}

function check_password(pwdInput) {
    var value =pwdInput.value;
    value = value.trim();
    if (value.length>0){
        togli_errore(pwdInput);
        return true;
    }
    mostra_errore(pwdInput,"Password non valido!")
    return false;
}
function check_descrizione_foto() {

}

function validazione_form_ricerca() {
    const citta = document.getElementById("cities");
    const dataInizio = document.getElementById("dataInizio");
    const dataFine = document.getElementById("dataFine");
    const numOspiti = document.getElementById("numOspiti");

    const resCitta = check_citta(citta);
    const resDataInizio = check_data(dataInizio);
    const resDataFine  = check_data(dataFine);
    const resDataIF = check_dateInizioEFine(dataInizio, dataFine);
    const resNumOspiti = check_numOspiti(numOspiti);
    return resCitta && resDataInizio && resDataFine && resDataIF && resNumOspiti;
}
function  validazione_form_registrazione() {

}
//usato sia per login utente che login amministratore
function validazione_form_login() {
    const userMail = document.getElementById("user");
    const userPass = document.getElementById("password");
    const res_mail = check_userMail(userMail);
    const res_pass = check_password(userPass);
    return res_mail && res_pass;
}
function  validazione_form_aggiungi_annuncio() {

}
function validazione_form_ricerca_dettaglio_annuncio() {

}
function validazione_form_modifica_annuncio() {

}
function validazione_form_cambio_password() {

}
function validazione_form_modifica_profilo() {

}
