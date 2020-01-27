// onsubmit="return validazioneForm();" su attributi form
//se ritorna true richiama la pagina php, se restituisce false non esegue l'action


function checkNome(nomeInput){
  //elevamento fissa char iniziale $fissa char finale
  var patt = new RegExp('^[a-zA-Z]{3,}$');
  if(patt.test(nomeInput.value)){
    togliErrore(nomeInput);
    return true;
  }
  else{
    mostraErrore(nomeInput, "nome inserito non corretto, almeno 3 char");
    return false;
  }
  return false;
}

function checkColore(coloreInput){
  var patt = new RegExp('^[a-zA-Z]{3,}$');
  if(patt.test(coloreInput.value)){
    togliErrore(coloreInput);
    return true;
  }
  else{
    mostraErrore(coloreInput, "nome inserito non corretto, almeno 3 char");
    return false;
  }
  return false;
}

function checkPeso(pesoInput){
  var patt = new RegExp('^[1-9][0-9]{0,2}$');
  if(patt.test(pesoInput.value)){
    togliErrore(pesoInput);
    return true;
  }
  else{
    mostraErrore(pesoInput, "nome inserito non corretto, almeno 3 char");
    return false;
  }
  return false;
}

function checkDescrizione(descInput){
  var patt = new RegExp('^[a-zA-Z]{10,100}$');
  if(patt.test(descInput.value)){
    togliErrore(descInput);
    return true;
  }
  else{
    mostraErrore(descInput, "nome inserito non corretto, almeno 3 char");
    return false;
  }
  return false;
}

//funzione generale per mostrare errore
function mostra_errore(input, testoErrore){
  togliErrore(input);
  var p = input.parentNode;
  /*var span = document.createElement("span");
  span.className = "Errore";
  p.appendChild(span);*/
  var strong = document.createElement("strong");
  strong.appendChild(document.createTextElement(testoErrore));
  p.appendChild(strong);
}

//da richiamare sempre prima di mostraErrore
function togli_errore(input){
    var p = input.parentNode;
    if(p.children.lenght > 2){
      p.removeChild(p.children[2]);
    }
}

function validazioneForm(){

  var nome = document.getElementByID("nome");
  var colore = document.getElementByID("colore");
  var peso = document.getElementByID("peso");
  var descrizione = document.getElementByID("descrizione");

  var risultatoNome = checkNome();
  var risultatoColore = checkColore();
  var risultatoPeso = checkPeso();
  var risultatoDesc = checkDescrizione();
  return risultatoNome && risultatoColore && risultatoPeso && risultatoDesc;
}
