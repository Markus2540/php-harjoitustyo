/*
 * Alkuperäinen tarkoitus oli tehdä tästä vain salasanojen tarkistustoiminto, mutta 
 * tehdään tähän muitakin toimintoja tunnusten luontisivua varten.
 * if (salasana !== null) tarkistus lisättiin tähän sitä varten, että jos tämän sivun toiminnot 
 * lisättäisiin perustoiminnot.js-tiedostoon, niin sivulla missä ei ole tunnustenluontisalasanaelementtiä 
 * kehittäjäkonsoli valittaisi Uncaught TypeError: salasana is null.
 */
const salasanaInfo = document.getElementById("salasanainfo");
const salasananToistoInfo = document.getElementById("salasanantoistoinfo");
const salasana = document.getElementById("uusisalasana");
const vahvistaSalasana = document.getElementById("uusivahvistasalasana");
const etunimi = document.getElementById("tunnustenluontietunimi");
const sukunimi = document.getElementById("tunnustenluontisukunimi");
const lahiosoite = document.getElementById("tunnustenluontilahiosoite");
const postitoimipaikka = document.getElementById("tunnustenluontipostitoimipaikka");
const pienet = /[a-z]/;
const suuret = /[A-Z]/;
const numerot = /[0-9]/;
const erikoismerkit = /[!"#¤%&/()=?]/;
const laiton = /[^a-zA-Z0-9!"#¤%&/()=?]/;


if (salasana !== null) {
    function tarkastaVastaavuus() {
        if (salasana.value === vahvistaSalasana.value) {
            salasananToistoInfo.textContent = "Salasanat vastaavat toisiaan.";
            salasananToistoInfo.style.color = "green";
        } else {
            salasananToistoInfo.textContent = "Salasanat eivät vastaa toisiaan.";
            salasananToistoInfo.style.color = "red";
        }
    }
       
    salasana.addEventListener('keyup', () => {
        if (laiton.test(salasana.value)) {
            salasanaInfo.style.color = "red";
            salasanaInfo.textContent = `Salasanassa on laittomia merkkejä.`;
            return;
        }
        let score = 0;
        if (pienet.test(salasana.value)) {score++;}
        if (suuret.test(salasana.value)) {score++;}
        if (numerot.test(salasana.value)) {score++;}
        if (erikoismerkit.test(salasana.value)) {score++;}
        if (salasana.value.length < 8) {
            salasanaInfo.style.color = "red";
            salasanaInfo.textContent = `Salasana on liian lyhyt.`;
        } else if (salasana.value.length > 7 && score < 3) {
            salasanaInfo.style.color = "orange";
            salasanaInfo.textContent = `Salasana ei ole tarpeeksi monimutkainen.`;
        } else {
            salasanaInfo.style.color = "green";
            salasanaInfo.textContent = `Salasana täyttää vaatimukset.`;
        }
    });
    salasana.addEventListener('keyup', tarkastaVastaavuus);
    vahvistaSalasana.addEventListener('keyup', tarkastaVastaavuus);
    
}

//Seuraava funktio muuttaa halutuista kentistä ensimmäisen kirjaimen suureksi.
function isoAlkukirjain(mista, mita) {
    mita = mita.replace(mita[0], mita[0].toUpperCase());
    mista.value = mita;
}
//Lisätään haluttuihin kenttiin tapahtumien kuuntelija, joka kutsuu edellä määritettyä funktiota.
//Aluksi kuitenkin tarkastetaan ollaanko nyt tunnusten luontisivulla vai salasanan vaihtosivulla 
//katsomalla onko etunimikenttää olemassa.
if (etunimi !== null) {
    etunimi.addEventListener('keyup', () => {if (etunimi.value.length > 0) {
            isoAlkukirjain(etunimi, etunimi.value);}});
    sukunimi.addEventListener('keyup', () => {if (sukunimi.value.length > 0) {
            isoAlkukirjain(sukunimi, sukunimi.value);}});
    lahiosoite.addEventListener('keyup', () => {if (lahiosoite.value.length > 0) {
            isoAlkukirjain(lahiosoite, lahiosoite.value);}});
    postitoimipaikka.addEventListener('keyup', () => {if (postitoimipaikka.value.length > 0) {
            isoAlkukirjain(postitoimipaikka, postitoimipaikka.value);}});
}


//Seuraavat toiminnot ovat salasanaohjeen näyttöä varten.
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
salasanaohje.addEventListener('click', () => {
    showNotification("Salasanassa voi käyttää pieniä ja suuria kirjaimia ääkkösiä \n\
lukuunottamatta, numeroita ja erikoismerkeistä !\"#¤%&/()=? merkkejä. \n\
Salasanan pituuden pitää olla vähintään 8 merkkiä ja siinä pitää olla merkkejä \n\
vähintään kolmesta edellä mainitusta ryhmästä (ryhmät ovat pienet kirjaimet, \n\
suuret kirjaimet, numerot ja erikoismerkit.");
});