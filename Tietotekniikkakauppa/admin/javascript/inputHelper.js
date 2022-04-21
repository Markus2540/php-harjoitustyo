const productName = document.getElementsByClassName("jsproductname");
const productManufacturer = document.getElementsByClassName("jsproductmanufacturer");
const productPrice = document.getElementsByClassName("jsproductprice");
const productShortDescription = document.getElementsByClassName("jsproductshortdescription");
const productDescription = document.getElementsByClassName("jsproductdescription");
const productPicture = document.getElementsByClassName("jsproductpicture");
const productPicture2 = document.getElementsByClassName("jsproductpicture2");
const productDiscount = document.getElementsByClassName("jsproductdiscount");
const lyhytKuvaus = document.getElementById("lyhytkuvaus");
const tuotekuvaus = document.getElementById("tuotekuvaus");
const uusiLyhytKuvaus = document.getElementById("uusilyhytkuvaus");
const uusiTuotekuvaus = document.getElementById("uusituotekuvaus");
const submitBtn = document.getElementById("submitbtn");


const lyhyenKuvauksenPituus = document.getElementById('lyhyenkuvauksenpituus');

lyhytKuvaus.addEventListener('keyup', () => {
    lyhyenKuvauksenPituus.textContent = `${lyhytKuvaus.value.length}/400 `;
});


const btnIlmoituksenKuittaus = document.getElementById("btnilmoituksenkuittaus");
const tummennus = document.getElementById("tummennus");
const ilmoitusTummennuksenKeskella = document.getElementById("ilmoitustummennuksenkeskella");
const ilmoituksenTeksti = document.getElementById("ilmoituksenteksti");
const salasanaohje = document.getElementById("salasanaohje");

function hideNotification() {
    tummennus.style.display = "none";
    ilmoitusTummennuksenKeskella.style.display = "none";
}
btnIlmoituksenKuittaus.addEventListener('click', hideNotification);

function showNotification(viesti) {
    tummennus.style.display = "block";
    ilmoitusTummennuksenKeskella.style.display = "block";
    ilmoituksenTeksti.textContent = viesti;
}

for (let i = 0; i < productName.length; i++) {
    productName[i].addEventListener('click', () => {
        showNotification("Tuotteen nimessä voi käyttää vain suomen kielen aakkosia, \n\
        numeroita, pilkkuja, pisteitä, välilyöntejä, @-merkkejä ja väliviivoja.");
    });
}

for (let i = 0; i < productManufacturer.length; i++) {
    productManufacturer[i].addEventListener('click', () => {
        showNotification("Valmistajan nimessä voi käyttää vain suomen kielen aakkosia, \n\
        numeroita, pilkkuja, pisteitä, välilyöntejä, @-merkkejä ja väliviivoja.");
    });
}

for (let i = 0; i < productPrice.length; i++) {
    productPrice[i].addEventListener('click', () => {
        showNotification("Tuotteen hinnassa voi käyttää vain numeroita ja \n\
        desimaalierottimena pistettä.");
    });
}

if (productDiscount !== null) {
    for (let i = 0; i < productDiscount.length; i++) {
        productDiscount[i].addEventListener('click', () => {
            showNotification("Tuotteen alennusprosentissa voidaan käyttää vain \n\
            kirjaimia ja pistettä desimaalierottimena. Tarkkuus maksimissaan \n\
            sadasosan tarkkuudella. Tullakseen voimaan, alennuksella pitää olla \n\
            vähintään päättymispäivä ja aika.");
    });
}
}

for (let i = 0; i < productShortDescription.length; i++) {
    productShortDescription[i].addEventListener('click', () => {
        showNotification("Tuotteen kuvauksessa voi käyttää vain aakkosia, \n\
        numeroita, välilyöntejä ja erikoismerkeistä ,.:®\s-");
    });
}

for (let i = 0; i < productDescription.length; i++) {
    productDescription[i].addEventListener('click', () => {
        showNotification("Tuotteen kuvauksessa voi käyttää vain aakkosia, \n\
        numeroita, välilyöntejä ja erikoismerkeistä ,.:®\s-");
    });
}

for (let i = 0; i < productPicture.length; i++) {
    productPicture[i].addEventListener('click', () => {
        showNotification("Käytä tuotteen kuvan nimessä vain pieniä ja suuria aakkosia \n\
        (ei åäö) ja numeroita. Pienen kuvan koko 180*180 pikseliä ja maksimissaan 70kt ja suuren \n\
        kuvan koko 600*600 pikseliä ja maksimissaan 400kt. Tiedostomuoto jpg.");
    });
}

if (productPicture2 !== null) {
    for (let i = 0; i < productPicture2.length; i++) {
        productPicture2[i].addEventListener('click', () => {
            showNotification("Käytä tuotteen kuvan nimessä vain pieniä ja suuria aakkosia \n\
            (ei åäö) ja numeroita. Pienen kuvan koko 180*180 pikseliä ja maksimissaan 70kt ja suuren \n\
            kuvan koko 600*600 pikseliä ja maksimissaan 400kt. Tiedostomuoto jpg.");
        });
    }
}

let longDescription = true;
let shortDescription = true;

function submitDisabler() {
    if (longDescription === false || shortDescription === false) {
        submitBtn.disabled = true;
    } else {
        submitBtn.disabled = false;
    }
}

if (tuotekuvaus !== null) {
    tuotekuvaus.addEventListener('keyup', () => {
        if (/[^a-zåäöA-ZÅÄÖ0-9 ,.:®\s-]/.test(tuotekuvaus.value)) {
            tuotekuvaus.style = "background-color: red; color: white;";
            longDescription = false;
        } else {
            tuotekuvaus.style = "background-color: white; color: black;";
            longDescription = true;
        }
        submitDisabler();
    });
}

if (uusiTuotekuvaus !== null) {
    uusiTuotekuvaus.addEventListener('keyup', () => {
        if (/[^a-zåäöA-ZÅÄÖ0-9 ,.:®\s-]/.test(uusiTuotekuvaus.value)) {
            uusiTuotekuvaus.style = "background-color: red; color: white;";
            longDescription = false;
        } else {
            uusiTuotekuvaus.style = "background-color: white; color: black;";
            longDescription = true;
        }
        submitDisabler();
    });
}

if (lyhytKuvaus !== null) {
    lyhytKuvaus.addEventListener('keyup', () => {
        if (/[^a-zåäöA-ZÅÄÖ0-9 ,.:®\s-]/.test(lyhytKuvaus.value)) {
            lyhytKuvaus.style = "background-color: red; color: white;";
            shortDescription = false;
        } else {
            lyhytKuvaus.style = "background-color: white; color: black;";
            shortDescription = true;
        }
        submitDisabler();
    });
}

if (uusiLyhytKuvaus !== null) {
    uusiLyhytKuvaus.addEventListener('keyup', () => {
        if (/[^a-zåäöA-ZÅÄÖ0-9 ,.:®\s-]/.test(uusiLyhytKuvaus.value)) {
            uusiLyhytKuvaus.style = "background-color: red; color: white;";
            shortDescription = false;
        } else {
            uusiLyhytKuvaus.style = "background-color: white; color: black;";
            shortDescription = true;
        }
        submitDisabler();
    });
}